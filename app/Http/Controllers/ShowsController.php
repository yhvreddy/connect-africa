<?php

namespace App\Http\Controllers;

use App\Models\Entertainment;
use App\Models\EntertainmentAdditionalDetails;
use Illuminate\Http\Request;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\EntertainmentRepository;
use App\Repositories\CategoriesRepository;
use App\Http\Requests\ShowsRequest;
use App\Http\Requests\ShowUpdateRequest;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Repositories\CategorizeRepository;
use App\Repositories\CategorizeAssignedListRepository;
use App\Repositories\UserRepository;
class ShowsController extends Controller
{

    use TruFlix, HttpResponses;

    protected $emdRepository;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;
    protected $categorizeRepository;
    protected $categorizeAssignedListRepository;
    protected $users;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository,
        CategorizeRepository $_categorizeRepository,
        CategorizeAssignedListRepository $_categorizeAssignedListRepository,
        UserRepository $_userRepository
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
        $this->categorizeRepository = $_categorizeRepository;
        $this->categorizeAssignedListRepository = $_categorizeAssignedListRepository;
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
        $category = $this->categoryRepository->where('slug', 'shows')->first();
        $shows = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')->get();
        $newRecords = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDate)->get();
        $newRecordsThisWeek = $this->entertainmentRepository->where('category_id', $category->id)
        ->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDateThisWeek)
        ->get();
        $admins = $this->users->where('role_id', 2)->where('is_active', 1)->select('id', 'name')->get();
        return view("admin.shows.index", compact('shows','newRecords','currentDate','startDate','startDateThisWeek','newRecordsThisWeek', 'admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ratings = app('truFlix')->ratings();
        $ratedList = app('truFlix')->ratedList();
        $genres = $this->emdRepository->where('type', 'genres')->orderBy('title', 'ASC')->get();
        $ottPlatforms = $this->emdRepository->where('type', 'ott_platforms')->orderBy('title', 'ASC')->get();
        $categorizes = $this->categorizeRepository->where('type', 'shows')->where('is_active', 1)->orderBy('title', 'ASC')->get();
        $years = range(1900, date('Y'));
        rsort($years);
        return view('admin.shows.add', compact('ratings', 'genres', 'ottPlatforms', 'ratedList', 'categorizes', 'years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShowsRequest $request)
    {
        try {
            $category = $this->categoryRepository->where('slug', 'shows')->first();
            if(!$category){
                return redirect()->back()->with('failed', 'Invalid data to store details.');
            }

            $user = app('truFlix')->getSessionUser();
            if(!$user){
                return redirect()->back()->with('failed', 'Invalid user session. Please logout and login again.');
            }

            $data = $request->all();
            $response = DB::transaction(function () use ($request, $data, $user, $category) {
                //Map Data to store details
                $showData = [
                    'user_id'           =>  $user->id,
                    'category_id'       =>  $category->id,
                    'title'             =>  $data['title'],
                    'description'       =>  $data['description'] ?? null,
                    'year'              =>  $data['year'] ?? null,
                    'rating'            =>  $data['rating'] ?? null,
                    // 'imdb_score'        =>  $data['imdb_score'] ?? null,
                    'rt_score'          =>  $data['rt_score'] ?? null,
                    'google_score'      =>  $data['google_score'] ?? null,
                    'truflix_score'     =>  $data['truflix_score'] ?? 0,
                    'slug'              =>  $this->generateSlug($data['title']),
                ];

                $entertainment = $this->entertainmentRepository->create($showData);
                if($entertainment){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/shows/poster_images');
                    $entertainment->poster_image = $posterImage['path'] ?? null;
                    $entertainment->save();

                    // Store selected genres
                    if (isset($data['genres']) && !empty($data['genres'])) {
                        foreach ($data['genres'] as $genreId) {
                            $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $entertainment->id,
                                'type' => 'show_genre',
                                'em_id' => $genreId,
                            ]);
                        }
                    }

                    //Save Additional Data
                    if(isset($data['wp_one']) && !empty($data['wp_one'])){
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'show_wp_one',
                            'em_id' => $data['wp_one'],
                        ]);
                    }

                    if(isset($data['wp_two']) && !empty($data['wp_two'])){
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'show_wp_two',
                            'em_id' => $data['wp_two'],
                        ]);
                    }

                    if(isset($data['wp_three']) && !empty($data['wp_three'])){
                        $wp_three = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'show_wp_three',
                            'em_id' => $data['wp_three'],
                        ]);
                    }

                    if(isset($data['free_option']) && !empty($data['free_option'])){
                        $wp_three = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'show_free_option',
                            'em_id' => $data['free_option'],
                        ]);
                    }

                    //Upload videos
                    if(isset($request->videos)){
                        foreach($request->videos as $key => $value){
                            if(isset($value['image']) && !empty($value['image'])){
                                $thumbImage = $this->uploadWithFile($value['image'], 'uploads/shows/video_posters');
                                if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                    $this->entertainmentAdditionalRepository->create([
                                        'entertainment_id' => $entertainment->id,
                                        'type'  =>  'shows_video',
                                        'url' => $value['link'] ?? null,
                                        'image' => $thumbImage['path'] ?? null,
                                    ]);
                                }
                            }
                        }
                    }

                    //Categorizes
                    if(isset($request->categorizes)){
                        foreach($request->categorizes as $key => $categorize){
                            $this->categorizeAssignedListRepository->create([
                                'categorize_id' => $categorize,
                                'entertainment_id' => $entertainment->id,
                                'type' => 'shows'
                            ]);
                        }
                    }

                    return $this->objectCreated('Show details saved successfully.', $entertainment);
                }

                return $this->validation('Sorry, Show details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.shows.index')->with('success', $response->message);
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
            $show = $this->entertainmentRepository->find($id); // Assuming you have a method to find a show by its ID in your repository
            if (!$show) {
                return redirect()->route('default.shows.index')->with('error', 'show not found.');
            }
            $genres = DB::table('entertainments_additional_details')
            ->where('entertainment_id', $show->id)
            ->where('type', 'show_genre')
            ->pluck('em_id')
            ->toArray();

            $availableGenres = DB::table('entertainments_additional_details')
            ->where('type', 'show_genre')
            ->distinct('em_id')
            ->pluck('em_id')
            ->toArray();

            $watchopt1 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $show->id)
            ->where('entertainments_additional_details.type', 'show_wp_one')
            ->value('entertainments_master_data.title');

            $watchopt2 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $show->id)
            ->where('entertainments_additional_details.type', 'show_wp_two')
            ->value('entertainments_master_data.title');

            $watchopt3 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $show->id)
            ->where('entertainments_additional_details.type', 'show_wp_three')
            ->value('entertainments_master_data.title');

            $watchoptfree = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $show->id)
            ->where('entertainments_additional_details.type', 'show_free_option')
            ->value('entertainments_master_data.title');

            $videos = DB::table('entertainments_additional_details')
                ->where('entertainment_id', $show->id)
                ->where('type', 'shows_video')
                ->get();

            return view('admin.shows.view', compact('show','genres','availableGenres','watchopt1','watchopt2','watchopt3','watchoptfree','videos'));
        } catch (\Exception $e) {
            return redirect()->route('default.shows.index')->with('error', 'Failed to fetch show details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entertainment $show)
    {
        try {
            //  Get all default values
            $ratings = app('truFlix')->ratings();
            $ratedList = app('truFlix')->ratedList();
            $genres = $this->emdRepository->where('type', 'genres')->orderBy('title', 'ASC')->get();
            $ottPlatforms = $this->emdRepository->where('type', 'ott_platforms')->orderBy('title', 'ASC')->get();
            $categorizes = $this->categorizeRepository->where('type', 'shows')->orderBy('title', 'ASC')->where('is_active', 1)->get();
            $years = range(1900, date('Y'));
            rsort($years);
            return view('admin.shows.edit', compact('show', 'ratings', 'ratedList', 'genres', 'ottPlatforms', 'categorizes', 'years'));
        } catch (\Throwable $th) {
            // Handle any errors that occur during the fetching process
            return redirect()->back()->with('error', 'Failed to fetch show details for editing: ' . $th->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ShowUpdateRequest $request, Entertainment $show)
    {
        try {
            $response = DB::transaction(function () use ($request, $show) {

                $data = $request->all();
                // dd($data);

                //Map Data to save details
                $show->title           =   $data['title'];
                $show->description     =   $data['description'];
                $show->year            =   $data['year'];
                $show->rating          =   $data['rating'];
                // $show->imdb_score      =   $data['imdb_score'];
                $show->truflix_score   =   $data['truflix_score'];
                $show->slug            =   $this->generateSlug($data['title']);

                if($show->save()){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/poster_images');
                    if(isset($posterImage['path']) && Storage::exists($posterImage['path'])){
                        Storage::delete($show->poster_image);
                    }
                    $show->poster_image = $posterImage['path'] ?? $show->poster_image;
                    $show->save();

                    // Store selected genres
                    if (isset($data['genres']) && !empty($data['genres'])) {

                        $savedGenres = $show->getAdditionalDataByType('show_genres')->get();
                        foreach ($savedGenres as $key => $savedGenre) {
                            if(!in_array($savedGenre->em_id, $data['genres'])){
                                $savedGenre->delete();
                            }
                        }

                        $savedGenres = $show->getAdditionalDataByType('show_genres')->pluck('em_id')->toArray();
                        foreach ($data['genres'] as $genreId) {
                            if(!in_array($genreId, $savedGenres)){
                                $this->entertainmentAdditionalRepository->create([
                                    'entertainment_id' => $show->id,
                                    'type' => 'show_genres',
                                    'em_id' => $genreId,
                                ]);
                            }
                        }
                    }

                    //Save Additional Data
                    if(isset($data['wp_one']) && !empty($data['wp_one'])){
                        $oneOption = $show->getAdditionalDataByType('show_wp_one')->first();
                        if($oneOption){
                            $oneOption->em_id = $data['wp_one'];
                            $oneOption->save();
                        }else{
                            $wp_one = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $show->id,
                                'type'  =>  'show_wp_one',
                                'em_id' => $data['wp_one'],
                            ]);
                        }
                    }

                    if(isset($data['wp_two']) && !empty($data['wp_two'])){
                        $twoOption = $show->getAdditionalDataByType('show_wp_two')->first();
                        if($twoOption){
                            $twoOption->em_id = $data['wp_two'];
                            $twoOption->save();
                        }else{
                            $wp_one = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $show->id,
                                'type'  =>  'show_wp_two',
                                'em_id' => $data['wp_two'],
                            ]);
                        }
                    }

                    if(isset($data['wp_three']) && !empty($data['wp_three'])){
                        $threeOption = $show->getAdditionalDataByType('show_wp_three')->first();
                        if($threeOption){
                            $threeOption->em_id = $data['wp_three'];
                            $threeOption->save();
                        }else{
                            $wp_three = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $show->id,
                                'type'  =>  'show_wp_three',
                                'em_id' => $data['wp_three'],
                            ]);
                        }
                    }

                    if(isset($data['free_option']) && !empty($data['free_option'])){
                        $freeOption = $show->getAdditionalDataByType('show_free_option')->first();
                        if($freeOption){
                            $freeOption->em_id = $data['free_option'];
                            $freeOption->save();
                        }else{
                            $wp_three = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $show->id,
                                'type'  =>  'show_free_option',
                                'em_id' => $data['free_option'],
                            ]);
                        }
                    }

                    // update existing videos data
                    if(isset($request->update_videos)){
                        foreach($request->update_videos as $value) {
                            $savedVideo = $show->getAdditionalDataByType('shows_video')->where('id', $value['id'])->first();
                            if(isset($value['image']) && !empty($value['image'])){
                                $thumbImage = $this->uploadWithFile($value['image'], 'uploads/shows');
                                if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                   if(!empty($savedVideo->image) &&  Storage::exists($savedVideo->image)) Storage::delete($savedVideo->image);
                                }
                                $savedVideo->image = $thumbImage['path'] ?? $savedVideo->image;
                                $savedVideo->save();
                            }else{
                                $savedVideo->url = $value['link'];
                                $savedVideo->save();
                            }
                        }
                    }

                    //New Upload videos
                    if(isset($request->videos)){
                        foreach($request->videos as $key => $value){
                            $thumbImage = $this->uploadWithFile($value['image'], 'uploads/shows');
                            if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                $this->entertainmentAdditionalRepository->create([
                                    'entertainment_id' => $show->id,
                                    'type'  =>  'shows_video',
                                    'url' => $value['link'] ?? null,
                                    'image' => $thumbImage['path'] ?? null,
                                ]);
                            }
                        }
                    }

                    $categorizeIds = $show->getAssignedCategorizedList('movies')->pluck('categorize_id')->toArray();
                    if(isset($request->categorizes)){
                        $this->categorizeAssignedListRepository->whereNotIn('categorize_id', $request->categorizes)
                        ->where('type', 'shows')->where('entertainment_id', $show->id)->delete();
                        foreach($request->categorizes as $key => $categorize){
                            $checkCategorize = $this->categorizeAssignedListRepository->where(['categorize_id' => $categorize, 'type'=>'shows'])->where('entertainment_id', $show->id)->first();
                            if(!$checkCategorize){
                                $this->categorizeAssignedListRepository->create([
                                    'categorize_id' => $categorize,
                                    'entertainment_id' => $show->id,
                                    'type' => 'shows'
                                ]);
                            }
                        }
                    }

                    return $this->objectCreated('Show details updated successfully.', $show);
                }

                return $this->validation('Sorry, Show details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.shows.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update show: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update show: ' . $th->getMessage());
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

            return redirect()->route('default.shows.index')->with('success', 'Show record deleted successfully.');
        } catch (\Throwable $th) {
            // Handle any errors that occur during the deletion process
            return redirect()->back()->with('error', 'Failed to delete Show record: ' . $th->getMessage());
        }
    }

    public function deleteVideoSource(EntertainmentAdditionalDetails $video){
        try {
            $sourceLike = $video->image;
            if($video->delete()){
                if(!empty($sourceLike) && Storage::exists($sourceLike)){
                    Storage::delete($sourceLike);
                }

                return response()->json(['status' => true, 'message' => 'Successfully deleted video details.'], 200);
            }

            return response()->json(['status' => false, 'message' => 'Failed to delete video details.'], 400);

        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 400);
        }
    }

    public function showFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'poster_image',
            'title',
            'year',
            'rating',
            'imdb_score',
            'created_at',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $category = $this->categoryRepository->where('slug', 'shows')->first();
        $shows = $this->entertainmentRepository->where('entertainments.category_id', $category->id)->select('entertainments.*');

        if(isset($data['isCategorize']) && !empty($data['isCategorize'])){
            $shows->leftJoin('categorize_assigned_list','categorize_assigned_list.entertainment_id', 'entertainments.id')
            ->where('categorize_assigned_list.categorize_id', $data['isCategorize']);
        }

        if(isset($data['admin_id']) && !empty($data['admin_id'])){
            $shows->where('entertainments.user_id', $data['admin_id']);
        }

        $totalData = count($shows->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $shows->Where('entertainments.title', 'Like', '%'.$data['search'].'%');
            }


            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $shows->whereBetween('entertainments.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $shows->whereMonth('entertainments.created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $shows->whereYear('entertainments.created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $shows->whereDate('entertainments.created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }


            $totalFiltered = count($shows->get());
        }

        $shows = $shows
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newShows = [];
        foreach($shows as $key => $show){

            $activateButton = $show->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $show->is_active ? 'deactivate' : 'activate';

            $actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="'.route('default.shows.show', ['show' => $show->id]).'"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit">
                                <a href="'.route('default.shows.edit', ['show' => $show->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('default.shows.destroy', ['show' => $show->id]).'" onclick=" return confirm(\'Are you sure to delete this show?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$show->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';
            $newShow = [
                'sno' => $key +1,
                'poster_image' => $show->poster_path($show->poster_image, true),
                'title' => $show->title,
                'year' => $show->year,
                'rated' => $show->rating,
                'imdb_score' => $show->imdb_score,
                'date_added' => date('M d, Y', strtotime($show->created_at)),
            ];

            if(isset($data['isCategorize']) && !empty($data['isCategorize'])){
                $categorize = $this->categorizeAssignedListRepository
                                ->where('entertainment_id', $show->id)
                                ->where('categorize_id', $data['isCategorize'])
                                ->where('type', 'shows')
                                ->first();

                $orderOptions = '<div class="order_select_update">
                    <select class="order_option" data-id="'.$show->id.'" data-assing_categorize_id="'.$categorize?->id.'" data-categorize_id="'.$data['isCategorize'].'">
                        <option value="">select order</option>';
                        for ($i=1; $i <= 10; $i++) {
                            $orderOptions .= '<option value="'.$i.'" '.($categorize->sort_id != $i?:'selected').' >'.$i.'</option>';
                        }
                $orderOptions .= '</select>
                </div>';
                $newShow['actions'] = $orderOptions;
            }else{
                if($data['isAdminRequest'] == 'false'){
                    $newShow['actions'] = $show->user->name ?? '';
                }else{
                    $newShow['actions'] = $actions;
                }
            }
            array_push($newShows, $newShow);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newShows);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $showId, $action)
    {
        $show = Entertainment::findOrFail($showId);

        if (!$show) {
            return response()->json(['error' => 'Show not found.'], 404);
        }

        if ($action === 'activate') {
            $show->is_active = 1;
        } elseif ($action === 'deactivate') {
            $show->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $show->save();

        return response()->json(['message' => 'Show status updated successfully.'], 200);
    }

    public function categorizeShows(Request $request, $categorize){
        $categorize = $this->categorizeRepository->where('slug', $categorize)->first();
        if(!$categorize){
            return redirect()->back()->with('failed', 'Invalid Request or No data found yet.');
        }

        $currentDate = Carbon::now();
        $startDateThisWeek = $currentDate->startOfWeek();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days agos
        $category = $this->categoryRepository->where('slug', 'shows')->first();
        $shows = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')->get();
        $newRecords = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDate)->get();
        $newRecordsThisWeek = $this->entertainmentRepository->where('category_id', $category->id)
        ->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDateThisWeek)
        ->get();
        $admins = $this->users->where('role_id', 2)->where('is_active', 1)->select('id', 'name')->get();
        return view("admin.shows.categorize_index", compact('shows','newRecords','currentDate','startDate','startDateThisWeek','newRecordsThisWeek', 'admins', 'categorize'));
    }
}
