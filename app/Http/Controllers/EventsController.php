<?php

namespace App\Http\Controllers;

use App\Models\Entertainment;
use App\Models\EntertainmentAdditionalDetails;
use Illuminate\Http\Request;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\EntertainmentRepository;
use App\Repositories\CategoriesRepository;
use App\Http\Requests\EventsRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Repositories\UserRepository;

class EventsController extends Controller
{
    use TruFlix, HttpResponses;


    protected $emdRepository;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;
    protected $users;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository,
        UserRepository $_userRepository
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
        $this->users = $_userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentDate = Carbon::now();
        $startDateThisWeek = $currentDate->startOfWeek();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days agos
        $category = $this->categoryRepository->where('slug', 'events')->first();
        $events = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')->get();
        $newRecords = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDate)->get();
        $newRecordsThisWeek = $this->entertainmentRepository->where('category_id', $category->id)
        ->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDateThisWeek)
        ->get();

        $admins = $this->users->where('role_id', 2)->where('is_active', 1)->select('id', 'name')->get();

        return view("admin.events.index", compact('events','newRecords','currentDate','startDate','startDateThisWeek','newRecordsThisWeek', 'admins'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventTypes = $this->emdRepository->where('type', 'event_types')->orderBy('title', 'ASC')->get();
        $ottPlatforms = $this->emdRepository->where('type', 'ott_platforms')->orderBy('title', 'ASC')->get();
        return view('admin.events.add', compact('ottPlatforms', 'eventTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventsRequest $request)
    {
        try {
            $category = $this->categoryRepository->where('slug', 'events')->first();
            if(!$category){
                return redirect()->back()->with('failed', 'Invalid data to store details.');
            }

            $user = app('truFlix')->getSessionUser();
            if(!$user){
                return redirect()->back()->with('failed', 'Invalid user session. Please logout and login again.');
            }

            $eventType = $this->emdRepository->find($request->event_type_id);
            if (!$eventType) {
                return redirect()->back()->with('failed', 'Invalid event type selected.');
            }

            $data = $request->all();
            $response = DB::transaction(function () use ($request, $data, $user, $category, $eventType) {
                // Save Event Details
                // Mapping Data to store
                $eventData = [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'event_type_id' => $eventType->id,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'time'  =>  $data['time'] ?? null,
                    'date'=>  $data['date'] ?? null,
                ];

                $entertainment = $this->entertainmentRepository->create($eventData);
                if($entertainment){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/events/poster_images');
                    $entertainment->poster_image = $posterImage['path'] ?? null;
                    $entertainment->save();

                    //Save Additional Data
                    if(isset($data['watch_one_title']) && !empty($data['watch_one_title'])){
                        $watchOneImage = $this->uploadWithName($request, 'watch_one_image', 'uploads/events/watch_one');
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'event_wp_one',
                            'title' =>  $data['watch_one_title'],
                            'image' =>  $watchOneImage['path'] ?? null
                        ]);
                    }

                    if(isset($data['watch_two_title']) && !empty($data['watch_two_title'])){
                        $watchTwoImage = $this->uploadWithName($request, 'watch_two_image', 'uploads/events/watch_two');
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'event_wp_two',
                            'title' =>  $data['watch_two_title'],
                            'image' =>  $watchTwoImage['path'] ?? null
                        ]);
                    }

                    if(isset($data['watch_three_title']) && !empty($data['watch_three_title'])){
                        $watchThreeImage = $this->uploadWithName($request, 'watch_three_image', 'uploads/events/watch_three');
                        $wp_three = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'event_wp_three',
                            'title' =>  $data['watch_three_title'],
                            'image' =>  $watchThreeImage['path'] ?? null
                        ]);
                    }

                    return $this->objectCreated('Event details saved successfully.', $entertainment);
                }

                return $this->validation('Sorry, Event details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.events.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $event = $this->entertainmentRepository->find($id); // Assuming you have a method to find a event by its ID in your repository
            if (!$event) {
                return redirect()->route('default.events.index')->with('error', 'event not found.');
            }

            $eventType = DB::table('entertainments_additional_details')
                ->where('entertainment_id', $id)
                ->where('type', 'event_type')
                ->first();

            $watchopt1 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $event->id)
            ->where('entertainments_additional_details.type', 'event_wp_one')
            ->value('entertainments_master_data.title');

            $watchopt2 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $event->id)
            ->where('entertainments_additional_details.type', 'event_wp_two')
            ->value('entertainments_master_data.title');

            $watchopt3 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $event->id)
            ->where('entertainments_additional_details.type', 'event_wp_three')
            ->value('entertainments_master_data.title');

            return view('admin.events.view', compact('event','watchopt1','watchopt2','watchopt3','eventType'));
        } catch (\Exception $e) {
            return redirect()->route('default.events.index')->with('error', 'Failed to fetch event details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entertainment $event)
    {
        try {
            $ottPlatforms = $this->emdRepository->where('type', 'ott_platforms')->orderBy('title', 'ASC')->get();
            if (!$event) {
                return redirect()->route('default.events.index')->with('error', 'Event not found.');
            }

            $eventTypes = $this->emdRepository->where('type', 'event_types')->orderBy('title', 'ASC')->get();


            return view('admin.events.edit', compact('event','ottPlatforms','eventTypes'));
        } catch (\Throwable $th) {
            // Handle any errors that occur during the fetching process
            return redirect()->back()->with('error', 'Failed to fetch Event details for editing: ' . $th->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(EventUpdateRequest $request, Entertainment $event)
    {
        try {
            $response = DB::transaction(function () use ($request, $event) {

                $data = $request->all();

                $eventType = $this->emdRepository->find($data['event_type_id']);
                if (!$eventType) {
                    return $this->validation('Invalid event type selected.');
                }

                //Map Data to save details
                $event->title           =   $data['title'];
                $event->event_type_id   =   $eventType->id;
                $event->description     =   $data['description'];
                $event->date            =   $data['date'];
                $event->time            =   $data['time'];
                $event->slug            =   $this->generateSlug($data['title']);

                if($event->save()){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/poster_images');
                    if(isset($posterImage['path']) && Storage::exists($posterImage['path'])){
                        Storage::delete($event->poster_image);
                    }
                    $event->poster_image = $posterImage['path'] ?? $event->poster_image;
                    $event->save();



                    //Save Additional Data
                    if(isset($data['watch_one_title']) && !empty($data['watch_one_title'])){
                        $watchOneImage = $this->uploadWithName($request, 'watch_one_image', 'uploads/events/watch_one');
                        $oneOption = $event->getAdditionalDataByType('event_wp_one')->first();
                        if(isset($watchOneImage['path']) && Storage::exists($watchOneImage['path'])){
                            if(isset($oneOption->image) && Storage::exists($oneOption->image)) Storage::delete($oneOption->image);
                        }

                        if($oneOption){
                            $oneOption->title   =   $data['watch_one_title'];
                            $oneOption->image   =   $watchOneImage['path'] ?? $oneOption->image;
                            $oneOption->save();
                        }else{
                            $oneOption = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $event->id,
                                'type'  =>  'event_wp_one',
                                'title' =>  $data['watch_one_title'],
                                'image' =>  $watchOneImage['path'] ?? null
                            ]);
                        }
                    }

                    if(isset($data['watch_two_title']) && !empty($data['watch_two_title'])){
                        $watchTwoImage = $this->uploadWithName($request, 'watch_two_image', 'uploads/events/watch_two');
                        $twoOption = $event->getAdditionalDataByType('event_wp_two')->first();
                        if(isset($watchTwoImage['path']) && Storage::exists($watchTwoImage['path'])){
                            if(isset($twoOption->image) && Storage::exists($twoOption->image)) Storage::delete($twoOption->image);
                        }

                        if($twoOption){
                            $twoOption->title   =   $data['watch_two_title'];
                            $twoOption->image   =   $watchTwoImage['path'] ?? $twoOption->image;
                            $twoOption->save();
                        }else{
                            $twoOption = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $event->id,
                                'type'  =>  'event_wp_two',
                                'title' =>  $data['watch_two_title'],
                                'image' =>  $watchTwoImage['path'] ?? null
                            ]);
                        }
                    }

                    if(isset($data['watch_three_title']) && !empty($data['watch_three_title'])){
                        $watchThreeImage = $this->uploadWithName($request, 'watch_one_image', 'uploads/events/watch_three');
                        $threeOption = $event->getAdditionalDataByType('event_wp_three')->first();
                        if(isset($watchThreeImage['path']) && Storage::exists($watchThreeImage['path'])){
                            if(isset($threeOption->image) && Storage::exists($threeOption->image)) Storage::delete($threeOption->image);
                        }

                        if($threeOption){
                            $threeOption->title   =   $data['watch_three_title'];
                            $threeOption->image   =   $watchThreeImage['path'] ?? $threeOption->image;
                            $threeOption->save();
                        }else{
                            $threeOption = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $event->id,
                                'type'  =>  'event_wp_three',
                                'title' =>  $data['watch_three_title'],
                                'image' =>  $watchThreeImage['path'] ?? null
                            ]);
                        }
                    }

                    return $this->objectCreated('Event details updated successfully.', $event);
                }

                return $this->validation('Sorry, Event details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.events.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update event: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update event: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entertainment $entertainment, $id)
    {
        try {
            // Find the entertainment record by its ID
            $entertainment = Entertainment::findOrFail($id);

            // Perform soft delete
            $entertainment->delete();

            return redirect()->route('default.events.index')->with('success', 'Event record deleted successfully.');
        } catch (\Throwable $th) {
            // Handle any errors that occur during the deletion process
            return redirect()->back()->with('error', 'Failed to delete Event record: ' . $th->getMessage());
        }
    }

    public function eventFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'poster_image',
            'title',
            'event_type',
            'time',
            'date',
            'created_at',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $category = $this->categoryRepository->where('slug', 'events')->first();
        $events = $this->entertainmentRepository->where('category_id', $category->id);

        if(isset($data['admin_id']) && !empty($data['admin_id'])){
            $events->where('user_id', $data['admin_id']);
        }

        $totalData = count($events->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $events->Where('title', 'Like', '%'.$data['search'].'%');
            }

            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $events->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $events->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $events->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $events->whereDate('created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }

            $totalFiltered = count($events->get());
        }

        $events = $events
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newEvents = [];
        foreach($events as $key => $event){

            $activateButton = $event->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $event->is_active ? 'deactivate' : 'activate';

            $actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="'.route('default.events.show', ['event' => $event->id]).'"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit">
                                <a href="'.route('default.events.edit', ['event' => $event->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('default.events.destroy', ['event' => $event->id]).'" onclick=" return confirm(\'Are you sure to delete this event?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                            <a href="javascript:void(0);" class="btn-activate" data-id="'.$event->id.'" data-action="'.$activateAction.'">
                                '.$activateButton.'
                            </a>
                        </li>
                        </ul>';
            $newEvent = [
                'sno' => $key +1,
                'poster_image' => $event->poster_path(true),
                'title' => $event->title,
                'event_type' => $event->eventType->title,
                'time' => $event->time,
                'date' => $event->date,
                'date_added' => date('M d, Y', strtotime($event->created_at)),
            ];
            if(isset($data['isAdminRequest']) && $data['isAdminRequest'] == 'false'){
                $newEvent['actions'] = $event->user->name ?? '';
            }else{
                $newEvent['actions'] = $actions;
            }
            array_push($newEvents, $newEvent);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newEvents);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $eventId, $action)
    {
        $event = Entertainment::findOrFail($eventId);

        if (!$event) {
            return response()->json(['error' => 'Event not found.'], 404);
        }

        if ($action === 'activate') {
            $event->is_active = 1;
        } elseif ($action === 'deactivate') {
            $event->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $event->save();

        return response()->json(['message' => 'Event status updated successfully.'], 200);
    }
}
