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
use App\Http\Resources\v1\EventsCollection;
use App\Http\Resources\v1\EventsResource;

class EventsController extends Controller
{
    use TruFlix, HttpResponses;

    protected $emdRepository;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *   tags={"Events"},
     *   path="/api/v1/entertainment/events",
     *   summary="Get All Events List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function index()
    {
        $category = $this->categoryRepository->where('slug', 'events')->first();
        $events = $this->entertainmentRepository
                    ->where('category_id', $category->id)
                    ->where('is_active', 1)
                    ->orderBy('id', 'DESC')
                    ->get();
        return $this->success('Events List', new EventsCollection($events));
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
     *     path="/api/v1/entertainment/events/{event}",
     *     tags={"Events"},
     *     summary="Event Details",
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         description="Event Id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Event Details",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="404", description="No Details Found")
     * )
     */
    public function show(Entertainment $movie)
    {

        if(!$movie){
            return $this->validation('No details found');
        }

        return $this->success('Event Details', new EventsResource($movie));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entertainment $entertainment)
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
}
