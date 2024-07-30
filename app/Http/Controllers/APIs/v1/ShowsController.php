<?php

namespace App\Http\Controllers\APIs\v1;

use App\Http\Controllers\Controller;
use App\Models\Entertainment;
use Illuminate\Http\Request;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\EntertainmentRepository;
use App\Repositories\CategoriesRepository;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\v1\ShowsCollection;
use App\Http\Resources\v1\ShowsResource;
use App\Repositories\CategorizeRepository;
use App\Repositories\CategorizeAssignedListRepository;
use Illuminate\Pagination\Paginator;

class ShowsController extends Controller
{
    use TruFlix, HttpResponses;

    protected $emdRepository;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;
    protected $categorizeRepository;
    protected $categorizeAssignedListRepository;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository,
        CategorizeRepository $_categorizeRepository,
        CategorizeAssignedListRepository $_categorizeAssignedListRepository
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
        $this->categorizeRepository = $_categorizeRepository;
        $this->categorizeAssignedListRepository = $_categorizeAssignedListRepository;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *   tags={"Shows"},
     *   path="/api/v1/entertainment/shows",
     *   summary="Get All Shows List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function index(Request $request)
    {
        $category = $this->categoryRepository->where('slug', 'shows')->first();
        $shows = $this->entertainmentRepository
                    ->where('entertainments.category_id', $category->id)
                    ->where('entertainments.is_active', 1);

        if(isset($request->search) && !empty($request->search)){
            $shows->where('entertainments.title', 'LIKE', '%'.$request->search.'%');
        }

        if(isset($request->year) && !empty($request->year)){
            $shows->orWhere('entertainments.year', $request->year);
        }

        if(isset($request->genres) && !empty($request->genres)){
            $shows->orWhere(function($query) use ($request) {
                $query->leftJoin(
                    'entertainments_additional_details',
                    'entertainments_additional_details.entertainment_id',
                    'entertainments.id'
                )
                ->where('entertainments_additional_details.em_id', $request->genres);
            });
        }

        $listOfShows = $shows->orderBy('id', 'DESC')
            ->paginate(30);
        return $this->success('Shows List', new ShowsCollection($listOfShows));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\GET(
     *   path="/api/v1/entertainment/shows/{show}",
     *   tags={"Shows"},
     *   summary="Show Details",
     *   @OA\Parameter(
     *      name="search",
     *      in="query",
     *      description="Search",
     *      required=false,
     *      @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *      name="year",
     *      in="query",
     *      description="Year",
     *      required=false,
     *      @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *      name="Generic",
     *      in="query",
     *      description="Generic",
     *      required=false,
     *      @OA\Schema(type="integer")
     *   ),
     *     @OA\Response(
     *          response=200,
     *          description="Show Details",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="404", description="No Details Found")
     * )
     */
    public function show(Entertainment $show)
    {
        if(!$show){
            return $this->validation('No details found');
        }
        return $this->success('Show Details', new ShowsResource($show));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entertainment $show)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entertainment $entertainment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entertainment $entertainment)
    {
        //
    }

    public function categorizedList(Request $request){

        $categorizes = $this->categorizeRepository->select('id', 'title', 'slug')->paginate(3);
        foreach($categorizes as $key => $categorize){
            $shows =   $this->entertainmentRepository
                        ->leftJoin('categorize_assigned_list', 'entertainments.id', 'categorize_assigned_list.entertainment_id')
                        ->select('entertainments.*')
                        ->where(['categorize_assigned_list.categorize_id' => $categorize->id, 'categorize_assigned_list.type' => 'shows'])->limit(10)->get();
            $categorize->items = new ShowsCollection($shows);
        }

        return response()->json($categorizes, 200);
    }
}
