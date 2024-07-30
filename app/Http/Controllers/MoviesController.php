<?php

namespace App\Http\Controllers;

use App\Models\Entertainment;
use App\Models\EntertainmentAdditionalDetails;
use Illuminate\Http\Request;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\EntertainmentRepository;
use App\Repositories\CategoriesRepository;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\MovieUpdateRequest;
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

class MoviesController extends Controller
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
        $category = $this->categoryRepository->where('slug', 'movies')->first();
        $movies = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')->get();
        $newRecords = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDate)->get();
        $newRecordsThisWeek = $this->entertainmentRepository->where('category_id', $category->id)
        ->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDateThisWeek)
        ->get();

        $admins = $this->users->where('role_id', 2)->where('is_active', 1)->select('id', 'name')->get();


        return view("admin.movies.index", compact('movies','newRecords','currentDate','startDate','startDateThisWeek','newRecordsThisWeek', 'admins'));
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
        $categorizes = $this->categorizeRepository->where('type', 'movies')->where('is_active', 1)->orderBy('title', 'ASC')->get();

        $years = range(1900, date('Y'));
        rsort($years);
        return view('admin.movies.add', compact('ratings', 'genres', 'ottPlatforms', 'ratedList', 'categorizes', 'years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        try {
            $category = $this->categoryRepository->where('slug', 'movies')->first();
            if(!$category){
                return redirect()->back()->with('failed', 'Invalid data to store details.');
            }

            $user = app('truFlix')->getSessionUser();
            if(!$user){
                return redirect()->back()->with('failed', 'Invalid user session. Please logout and login again.');
            }

            $response = DB::transaction(function () use ($request, $user, $category) {
                $data = $request->all();

                //Map Data to save details
                $movieData = [
                    'user_id'       =>  $user->id,
                    'category_id'   =>  $category->id,
                    'title'         =>  $data['title'],
                    'description'   =>  $data['description'] ?? null,
                    'year'          =>  $data['year'] ?? null,
                    'rating'        =>  $data['rating'] ?? null,
                    // 'imdb_score'    =>  $data['imdb_score'] ?? null,
                    'rt_score'      =>  0,
                    'google_score'  =>  0,
                    'truflix_score' =>  $data['truflix_score'] ?? 0,
                    'slug'          =>  $this->generateSlug($data['title']),
                ];


                $entertainment = $this->entertainmentRepository->create($movieData);
                if($entertainment){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/poster_images');
                    $entertainment->poster_image = $posterImage['path'] ?? null;
                    $entertainment->save();

                    // Store selected genres
                    if (isset($data['genres']) && !empty($data['genres'])) {
                        foreach ($data['genres'] as $genreId) {
                            $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $entertainment->id,
                                'type' => 'movie_genres',
                                'em_id' => $genreId,
                            ]);
                        }
                    }

                    //Save Additional Data
                    if(isset($data['wp_one']) && !empty($data['wp_one'])){
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'movie_wp_one',
                            'em_id' => $data['wp_one'],
                        ]);
                    }

                    if(isset($data['wp_two']) && !empty($data['wp_two'])){
                        $wp_one = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'movie_wp_two',
                            'em_id' => $data['wp_two'],
                        ]);
                    }

                    if(isset($data['wp_three']) && !empty($data['wp_three'])){
                        $wp_three = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'movie_wp_three',
                            'em_id' => $data['wp_three'],
                        ]);
                    }

                    if(isset($data['free_option']) && !empty($data['free_option'])){
                        $wp_three = $this->entertainmentAdditionalRepository->create([
                            'entertainment_id' => $entertainment->id,
                            'type'  =>  'movie_free_option',
                            'em_id' =>  $data['free_option'],
                        ]);
                    }

                    //Upload videos
                    if(isset($request->videos)){
                        foreach($request->videos as $key => $value){
                            if(isset($value['image']) && !empty($value['image'])){
                                $thumbImage = $this->uploadWithFile($value['image'], 'uploads/movies');
                                if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                    $this->entertainmentAdditionalRepository->create([
                                        'entertainment_id' => $entertainment->id,
                                        'type'  =>  'movies_video',
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
                                'type' => 'movies'
                            ]);
                        }
                    }

                    return $this->objectCreated('Movie details saved successfully.', $entertainment);
                }

                return $this->validation('Sorry, Movie details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.movies.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entertainment $movie)
    {
        try {
            if (!$movie) {
                return redirect()->route('default.movies.index')->with('error', 'Movie not found.');
            }
            $genres = DB::table('entertainments_additional_details')
            ->where('entertainment_id', $movie->id)
            ->where('type', 'movie_genre')
            ->pluck('em_id')
            ->toArray();

            $availableGenres = DB::table('entertainments_additional_details')
            ->where('type', 'movie_genre')
            ->distinct('em_id')
            ->pluck('em_id')
            ->toArray();

            $watchopt1 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $movie->id)
            ->where('entertainments_additional_details.type', 'movie_wp_one')
            ->value('entertainments_master_data.title');

            $watchopt2 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $movie->id)
            ->where('entertainments_additional_details.type', 'movie_wp_two')
            ->value('entertainments_master_data.title');

            $watchopt3 = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $movie->id)
            ->where('entertainments_additional_details.type', 'movie_wp_three')
            ->value('entertainments_master_data.title');

            $watchoptfree = DB::table('entertainments_additional_details')
            ->join('entertainments_master_data', 'entertainments_additional_details.em_id', '=', 'entertainments_master_data.id')
            ->where('entertainments_additional_details.entertainment_id', $movie->id)
            ->where('entertainments_additional_details.type', 'movie_free_option')
            ->value('entertainments_master_data.title');

            $videos = DB::table('entertainments_additional_details')
                ->where('entertainment_id', $movie->id)
                ->where('type', 'movies_video')
                ->get();

            return view('admin.movies.view', compact('movie','genres','availableGenres','watchopt1','watchopt2','watchopt3','watchoptfree','videos'));
        } catch (\Exception $e) {
            return redirect()->route('default.movies.index')->with('error', 'Failed to fetch movie details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entertainment $movie)
    {
        try {
            //  Get all default values
            $ratings = app('truFlix')->ratings();
            $ratedList = app('truFlix')->ratedList();
            $genres = $this->emdRepository->where('type', 'genres')->orderBy('title', 'ASC')->get();
            $ottPlatforms = $this->emdRepository->where('type', 'ott_platforms')->orderBy('title', 'ASC')->get();
            $categorizes = $this->categorizeRepository->where('type', 'movies')->orderBy('title', 'ASC')->where('is_active', 1)->get();
            $years = range(1900, date('Y'));
            rsort($years);
            return view('admin.movies.edit', compact('movie', 'ratings', 'ratedList', 'genres', 'ottPlatforms', 'categorizes', 'years'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to fetch movie details for editing: ' . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(MovieUpdateRequest $request, Entertainment $movie)
    {
        try {
            $response = DB::transaction(function () use ($request, $movie) {

                $data = $request->all();

                //Map Data to save details
                $movie->title           =   $data['title'];
                $movie->description     =   $data['description'];
                $movie->year            =   $data['year'];
                $movie->rating          =   $data['rating'];
                // $movie->imdb_score      =   $data['imdb_score'];
                $movie->truflix_score   =   $data['truflix_score'];
                $movie->slug            =   $this->generateSlug($data['title']);

                if($movie->save()){
                    $posterImage = $this->uploadWithName($request, 'poster_image', 'uploads/poster_images');
                    if(isset($posterImage['path']) && Storage::exists($posterImage['path'])){
                        Storage::delete($movie->poster_image);
                    }
                    $movie->poster_image = $posterImage['path'] ?? $movie->poster_image;
                    $movie->save();

                    // Store selected genres
                    if (isset($data['genres']) && !empty($data['genres'])) {

                        $savedGenres = $movie->getAdditionalDataByType('movie_genres')->get();
                        foreach ($savedGenres as $key => $savedGenre) {
                            if(!in_array($savedGenre->em_id, $data['genres'])){
                                $savedGenre->delete();
                            }
                        }

                        $savedGenres = $movie->getAdditionalDataByType('movie_genres')->pluck('em_id')->toArray();
                        foreach ($data['genres'] as $genreId) {
                            if(!in_array($genreId, $savedGenres)){
                                $this->entertainmentAdditionalRepository->create([
                                    'entertainment_id' => $movie->id,
                                    'type' => 'movie_genres',
                                    'em_id' => $genreId,
                                ]);
                            }
                        }
                    }

                    //Save Additional Data
                    if(isset($data['wp_one']) && !empty($data['wp_one'])){
                        $oneOption = $movie->getAdditionalDataByType('movie_wp_one')->first();
                        if($oneOption){
                            $oneOption->em_id = $data['wp_one'];
                            $oneOption->save();
                        }else{
                            $wp_one = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $movie->id,
                                'type'  =>  'movie_wp_one',
                                'em_id' => $data['wp_one'],
                            ]);
                        }
                    }

                    if(isset($data['wp_two']) && !empty($data['wp_two'])){
                        $twoOption = $movie->getAdditionalDataByType('movie_wp_two')->first();
                        if($twoOption){
                            $twoOption->em_id = $data['wp_two'];
                            $twoOption->save();
                        }else{
                            $wp_one = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $movie->id,
                                'type'  =>  'movie_wp_two',
                                'em_id' => $data['wp_two'],
                            ]);
                        }
                    }

                    if(isset($data['wp_three']) && !empty($data['wp_three'])){
                        $threeOption = $movie->getAdditionalDataByType('movie_wp_three')->first();
                        if($threeOption){
                            $threeOption->em_id = $data['wp_three'];
                            $threeOption->save();
                        }else{
                            $wp_three = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $movie->id,
                                'type'  =>  'movie_wp_three',
                                'em_id' => $data['wp_three'],
                            ]);
                        }
                    }

                    if(isset($data['free_option']) && !empty($data['free_option'])){
                        $freeOption = $movie->getAdditionalDataByType('movie_free_option')->first();
                        if($freeOption){
                            $freeOption->em_id = $data['free_option'];
                            $freeOption->save();
                        }else{
                            $wp_three = $this->entertainmentAdditionalRepository->create([
                                'entertainment_id' => $movie->id,
                                'type'  =>  'movie_free_option',
                                'em_id' => $data['free_option'],
                            ]);
                        }
                    }

                    // update existing videos data
                    if(isset($request->update_videos)){
                        foreach($request->update_videos as $value) {
                            $savedVideo = $movie->getAdditionalDataByType('movies_video')->where('id', $value['id'])->first();
                            if(isset($value['image']) && !empty($value['image'])){
                                $thumbImage = $this->uploadWithFile($value['image'], 'uploads/movies');
                                if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                   if(!empty($savedVideo->image) &&  Storage::exists($savedVideo->image)) Storage::delete($savedVideo->image);
                                }
                                $savedVideo->image = $thumbImage['path'] ?? $savedVideo->image;
                                $savedVideo->url = $value['link'] ?? $savedVideo->url;
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
                            if(isset($value['image']) && !empty($value['image'])){
                                $thumbImage = $this->uploadWithFile($value['image'], 'uploads/movies');
                                if(!empty($thumbImage['path']) && Storage::exists($thumbImage['path'])){
                                    $this->entertainmentAdditionalRepository->create([
                                        'entertainment_id' => $movie->id,
                                        'type'  =>  'movies_video',
                                        'url' => $value['link'] ?? null,
                                        'image' => $thumbImage['path'] ?? null,
                                    ]);
                                }
                            }
                        }
                    }

                    $categorizeIds = $movie->getAssignedCategorizedList('movies')->pluck('categorize_id')->toArray();
                    if(isset($request->categorizes)){
                        $this->categorizeAssignedListRepository->whereNotIn('categorize_id', $request->categorizes)
                        ->where('type', 'movies')->where('entertainment_id', $movie->id)->delete();
                        foreach($request->categorizes as $key => $categorize){
                            $checkCategorize = $this->categorizeAssignedListRepository->where(['categorize_id' => $categorize, 'type'=>'movies'])->where('entertainment_id', $movie->id)->first();
                            if(!$checkCategorize){
                                $this->categorizeAssignedListRepository->create([
                                    'categorize_id' => $categorize,
                                    'entertainment_id' => $movie->id,
                                    'type' => 'movies'
                                ]);
                            }
                        }
                    }

                    return $this->objectCreated('Movie details updated successfully.', $movie);
                }

                return $this->validation('Sorry, Movie details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('default.movies.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update movie: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update movie: ' . $th->getMessage());
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
            if(isset($entertainment->poster_image) && Storage::exists($entertainment->poster_image)){
                Storage::delete($entertainment->poster_image);
            }

            foreach($entertainment->getAdditionalData as $key => $value){
                if(isset($value->image) && Storage::exists($value->image)){
                    Storage::delete($value->image);
                }
                $value->delete();
            }

            // Perform soft delete
            $entertainment->forceDelete();

            return redirect()->route('default.movies.index')->with('success', 'Movies record deleted successfully.');
        } catch (\Throwable $th) {
            // Handle any errors that occur during the deletion process
            return redirect()->back()->with('error', 'Failed to delete Movies record: ' . $th->getMessage());
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

    public function fetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'entertainments.id',
            'entertainments.poster_image',
            'entertainments.title',
            'entertainments.year',
            'entertainments.rating',
            'entertainments.imdb_score',
            'entertainments.created_at',
            'entertainments.id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $category = $this->categoryRepository->where('slug', 'movies')->first();
        $movies = $this->entertainmentRepository->where('entertainments.category_id', $category->id)->select('entertainments.*');

        // dd(app('truFlix')->getSessionUser()->role->slug, $data);

        if(isset($data['isCategorize']) && !empty($data['isCategorize'])){
            $movies->leftJoin('categorize_assigned_list','categorize_assigned_list.entertainment_id', 'entertainments.id')
            ->where('categorize_assigned_list.categorize_id', $data['isCategorize']);
        }

        if(isset($data['admin_id']) && !empty($data['admin_id'])){
            $movies->where('entertainments.user_id', $data['admin_id']);
        }

        $totalData = count($movies->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $movies->Where('entertainments.title', 'Like', '%'.$data['search'].'%');
            }

            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $movies->whereBetween('entertainments.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $movies->whereMonth('entertainments.created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $movies->whereYear('entertainments.created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $movies->whereDate('entertainments.created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }

            $totalFiltered = count($movies->get());
        }

        $movies = $movies
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newMovies = [];
        foreach($movies as $key => $movie){
            $activateButton = $movie->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $movie->is_active ? 'deactivate' : 'activate';

            $actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="'.route('default.movies.show', ['movie' => $movie->id]).'"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit">
                                <a href="'.route('default.movies.edit', ['movie' => $movie->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('default.movies.destroy', ['movie' => $movie->id]).'" onclick=" return confirm(\'Are you sure to delete movie?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$movie->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';
            $newMovie = [
                'sno' => $key +1,
                'poster_image' => $movie->poster_path(true),
                'title' => $movie->title,
                'year' => $movie->year,
                // 'rated' => $movie->rating,
                // 'imdb_score' => $movie->imdb_score,
                'date_added' => date('M d, Y', strtotime($movie->created_at)),
            ];

            if(isset($data['isCategorize']) && !empty($data['isCategorize'])){
                $categorize = $this->categorizeAssignedListRepository
                                ->where('entertainment_id', $movie->id)
                                ->where('categorize_id', $data['isCategorize'])
                                ->where('type', 'movies')
                                ->first();

                $orderOptions = '<div class="order_select_update">
                    <select class="order_option" data-id="'.$movie->id.'" data-assing_categorize_id="'.$categorize?->id.'" data-categorize_id="'.$data['isCategorize'].'">
                        <option value="">select order</option>';
                        for ($i=1; $i <= 10; $i++) {
                            $orderOptions .= '<option value="'.$i.'" '.($categorize->sort_id != $i?:'selected').' >'.$i.'</option>';
                        }
                $orderOptions .= '</select>
                </div>';
                $newMovie['actions'] = $orderOptions;
            }else{
                if($data['isAdminRequest'] == 'false'){
                    $newMovie['actions'] = $movie->user->name ?? '';
                }else{
                    $newMovie['actions'] = $actions;
                }
            }

            array_push($newMovies, $newMovie);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newMovies);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $movieId, $action)
    {
        $movie = Entertainment::findOrFail($movieId);

        if (!$movie) {
            return response()->json(['error' => 'Movie not found.'], 404);
        }

        if ($action === 'activate') {
            $movie->is_active = 1;
        } elseif ($action === 'deactivate') {
            $movie->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $movie->save();

        return response()->json(['message' => 'Movie status updated successfully.'], 200);
    }


    public function categorizeMovies(Request $request, $categorize){
        $categorize = $this->categorizeRepository->where('slug', $categorize)->first();
        if(!$categorize){
            return redirect()->back()->with('failed', 'Invalid Request or No data found yet.');
        }

        $currentDate = Carbon::now();
        $startDateThisWeek = $currentDate->startOfWeek();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days agos
        $category = $this->categoryRepository->where('slug', 'movies')->first();
        $movies = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')->get();
        $newRecords = $this->entertainmentRepository->where('category_id', $category->id)->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDate)->get();
        $newRecordsThisWeek = $this->entertainmentRepository->where('category_id', $category->id)
        ->orderBy('id', 'DESC')
        ->where('created_at', '>=', $startDateThisWeek)
        ->get();
        $admins = $this->users->where('role_id', 2)->where('is_active', 1)->select('id', 'name')->get();


        return view("admin.movies.categorize_index", compact('categorize','movies','newRecords','currentDate','startDate','startDateThisWeek','newRecordsThisWeek', 'admins'));
    }


    public function updateOrderOfEntertainment(Request $request, $type){
        $order = $request->order_id;
        $id = $request->id;

        $entertainment = $this->entertainmentRepository->find($id);
        $categorize = $this->categorizeAssignedListRepository->find($request->assing_categorize_id);
        if(!$entertainment && !$categorize){
            return response()->json(['status' => false, 'message' => 'Invalid request to update order.'], 200);
        }


        $previousCategorize = $this->categorizeAssignedListRepository
                                ->where('type', $entertainment->category->slug)
                                ->where('categorize_id', $categorize->categorize_id)
                                ->where('sort_id', $order)
                                ->first();

        if($previousCategorize){
            $previousCategorize->sort_id = $categorize->sort_id;
            $previousCategorize->save();
        }

        $categorize->sort_id =   $order;
        $categorize->save();

        return response()->json(['status' => true, 'message' => 'successfully updated record order.'], 200);
    }

}
