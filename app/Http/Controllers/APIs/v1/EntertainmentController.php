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
use App\Http\Resources\v1\MoviesCollection;
use App\Http\Resources\v1\ShowsCollection;
use App\Http\Resources\v1\MoviesResource;
use App\Repositories\CategorizeRepository;
use App\Repositories\CategorizeAssignedListRepository;
use Illuminate\Pagination\Paginator;

class EntertainmentController extends Controller
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

    public function categorizedList(Request $request){

        $categorizes = $this->categorizeRepository->where('is_show_frontend', 1)
                        ->where('type', $request->type)
                        ->select('id', 'title', 'slug')
                        ->paginate(3);

        foreach($categorizes as $key => $categorize){
            $entertainment =   $this->entertainmentRepository
                        ->leftJoin('categorize_assigned_list', 'entertainments.id', 'categorize_assigned_list.entertainment_id')
                        ->select('entertainments.*')
                        ->where([
                            'categorize_assigned_list.categorize_id' => $categorize->id,
                            'categorize_assigned_list.type' => $request->type
                        ])
                        ->orderBy('categorize_assigned_list.sort_id', 'ASC')
                        ->get();

            $categorize->count = $entertainment->count();
            if($request->type === 'movies'){
                $categorize->items = new MoviesCollection($entertainment);
            }elseif($request->type === 'shows'){
                $categorize->items = new ShowsCollection($entertainment);
            }else{
                $categorize->items = [];
            }
        }

        return response()->json($categorizes, 200);
    }
}
