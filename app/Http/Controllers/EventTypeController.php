<?php

namespace App\Http\Controllers;
use App\Repositories\EntertainmentMasterRepository;
use App\Models\EntertainmentMasterData;
use Illuminate\Http\Request;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Http\Requests\EventTypeRequest;
use App\Http\Requests\EventTypeUpdateRequest;

class EventTypeController extends Controller
{
    use TruFlix, HttpResponses;

    protected $emdRepository;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventTypes = $this->emdRepository->where('type', 'event_types')->get();
        return view('zq.master-data.event-type.list', compact('eventTypes'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventTypes = $this->emdRepository->where('type', 'event_types')->get();

        return view('zq.master-data.event-type.add', compact('eventTypes'));

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(EventTypeRequest $request)
    {
        try {

            $response = DB::transaction(function () use ($request) {
                $data = $request->all();

                $slug = $this->generateSlug($data['slug']);
                $checkSlug = $this->emdRepository->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $eventData = [
                    'title' => $data['title'],
                    'slug' => $slug,
                    'type' => $data['type'],
                ];

                $eventType = $this->emdRepository->create($eventData);
                if($eventType){
                    return $this->objectCreated('event type saved successfully.', $eventType);
                }

                return $this->validation('Sorry, event type failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.event-type.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntertainmentMasterData $eventType)
    {
        return view('zq.master-data.event-type.edit', compact('eventType'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventTypeUpdateRequest $request, EntertainmentMasterData $eventType)
    {
        try {
            $response = DB::transaction(function () use ($request, $eventType) {

                $data = $request->all();

                $slug = $this->generateSlug($data['slug']);
                $checkSlug = $this->emdRepository->whereNot('id', $eventType->id)->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $eventType->title           =   $data['title'];
                $eventType->slug            =   $slug;

                if($eventType->save()){
                    return $this->objectCreated('Event details updated successfully.', $eventType);
                }

                return $this->validation('Sorry, Event details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.event-type.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update Event Type: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update Event Type: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function softDelete(EntertainmentMasterData $event_type)
    {
        try {
            $response = DB::transaction(function () use ($event_type) {
                if ($event_type->delete()) {
                    return $this->success('Event type deleted successfully.');
                }

                return $this->validation('Invalid Request to delete Event Type.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.even-type.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function eventTypeFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'title',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $eventTypes = $this->emdRepository->where('type','event_types');


        $totalData = count($eventTypes->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $eventTypes->where('type', 'Like', '%'.$data['search'].'%')
                           ->orWhere('title', 'Like', '%'.$data['search'].'%');
            }

        // Filter by user status (active or deactivated)
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 'active':
                    $eventTypes->where('is_active', 1);
                    break;
                case 'deactivated':
                    $eventTypes->where('is_active', 0);
                    break;
            }
        }


            $totalFiltered = count($eventTypes->get());
        }

        $eventTypes = $eventTypes
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newEventTypes = [];
        foreach ($eventTypes as $key => $eventType) {
            $activateButton = $eventType->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $eventType->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="javascript;0;"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit">
                                <a href="'.route('zq.event-type.edit', ['event_type' => $eventType->id]).'">
                                    <i class="icon-pencil-alt"></i>
                                </a>
                            </li>
                            <li class="delete">
                                <a href="'.route('zq.event-types.soft-delete', ['event-type' => $eventType->id]).'" onclick=" return confirm(\'Are you sure to delete this Event Type?\');">
                                    <i class="icon-trash"></i>
                                </a>
                            </li>

                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$eventType->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';

            // Create an array to hold each record
            $newEventType = [
                'sno' => $key + 1,
                'title' => $eventType->title,
                'actions' => $actions
            ];

            // Push the record into $newEventTypes array
            $newEventTypes[] = $newEventType;
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newEventTypes);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $eventTypeId, $action)
    {
        $eventTypes = EntertainmentMasterData::findOrFail($eventTypeId);

        if (!$eventTypes) {
            return response()->json(['error' => 'event type not found.'], 404);
        }

        if ($action === 'activate') {
            $eventTypes->is_active = 1;
        } elseif ($action === 'deactivate') {
            $eventTypes->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $eventTypes->save();

        return response()->json(['message' => 'event type status updated successfully.'], 200);
    }

}
