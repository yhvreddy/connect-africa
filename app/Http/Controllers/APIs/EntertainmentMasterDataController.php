<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EntertainmentMasterData;
use App\Http\Resources\v1\EntertainmentMasterDataResource;
use App\Http\Resources\v1\EntertainmentMasterDataCollection;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Log;

class EntertainmentMasterDataController extends Controller
{
    use HttpResponses;

    protected $entertainmentMasterData;
    
    public function __construct(EntertainmentMasterData $_entertainmentMasterData) {  
        $this->entertainmentMasterData = $_entertainmentMasterData;
    }

    // Entertainment Master Data API Functions Start Here
    /**
     * @OA\Get(
     *   tags={"Entertainment Master Data"},
     *   path="/api/v1/entertainment/master/genres",
     *   summary="Get All Genres List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function getGenresList(){
        $genres = $this->entertainmentMasterData->where('type', 'genres')->get();
        return $this->success('Genres List', new EntertainmentMasterDataCollection($genres));
    }

    /**
     * @OA\Get(
     *   tags={"Entertainment Master Data"},
     *   path="/api/v1/entertainment/master/ott-platforms",
     *   summary="Get All OTT Platforms List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function getOttPlatFormList(){
        $ottPlatForms = $this->entertainmentMasterData->where('type', 'ott_platforms')->get();
        return $this->success('OTT PlatForms List', new EntertainmentMasterDataCollection($ottPlatForms));
    }

    /**
     * @OA\Get(
     *   tags={"Entertainment Master Data"},
     *   path="/api/v1/entertainment/master/event-types",
     *   summary="Get All Event Types List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function getEventTypeList(){
        $eventTypes = $this->entertainmentMasterData->where('type', 'event_types')->get();
        return $this->success('Event Types List', new EntertainmentMasterDataCollection($eventTypes));
    }
}
