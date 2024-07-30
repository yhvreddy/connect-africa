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
use App\Http\Requests\OTTRequest;
use App\Http\Requests\OTTUpdateRequest;

class OttController extends Controller
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
        $ott = $this->emdRepository->where('type', 'ott_platforms')->get();
        return view('zq.master-data.ott.list', compact('ott'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ott = $this->emdRepository->where('type', 'ott_platforms')->get();

        return view('zq.master-data.ott.add', compact('ott'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OTTRequest $request)
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
                $ottData = [
                    'title' => $data['title'],
                    'slug' => $slug,
                    'type' => $data['type'],
                ];

               
                $ott = $this->emdRepository->create($ottData);
                if($ott){
                    $image = $this->uploadWithName($request, 'image', 'uploads/master-data/ott');
                    $ott->image = $image['path'] ?? null;
                    $ott->save();

                    return $this->objectCreated('OTT details saved successfully.', $ott);
                }

                return $this->validation('Sorry, OTT details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.ott.index')->with('success', $response->message);
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
    public function edit(EntertainmentMasterData $ott)
    {
        return view('zq.master-data.ott.edit', compact('ott'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OTTUpdateRequest $request, EntertainmentMasterData $ott)
    {
        try {
            $response = DB::transaction(function () use ($request, $ott) {

                $data = $request->all();

                $slug = $this->generateSlug($data['slug']);
                $checkSlug = $this->emdRepository->whereNot('id', $ott->id)->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $ott->title           =   $data['title'];
                $ott->slug            =   $slug;
                
                if($ott->save()){
                    $image = $this->uploadWithName($request, 'image', 'uploads/master-data/ott');
                    if(isset($image['path']) && Storage::exists($image['path'])){
                        Storage::delete($ott->image);
                    }
                    $ott->image = $image['path'] ?? $ott->image;
                    $ott->save();
                    return $this->objectCreated('OTT details updated successfully.', $ott);
                }

                return $this->validation('Sorry, OTT details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.ott.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update OTT: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update OTT: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function softDelete(EntertainmentMasterData $ott)
    {
        try {
            $response = DB::transaction(function () use ($ott) {
                if ($ott->delete()) {
                    return $this->success('OTT deleted successfully.');
                }

                return $this->validation('Invalid Request to delete OTT.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.ott.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);
            
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

     public function ottFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'image',
            'title',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');
        
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $otts = $this->emdRepository->where('type','ott_platforms');


        $totalData = count($otts->get());
        $totalFiltered = $totalData; 

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $otts->where('type', 'Like', '%'.$data['search'].'%')
                           ->orWhere('title', 'Like', '%'.$data['search'].'%');
            }

        // Filter by user status (active or deactivated)
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 'active':
                    $otts->where('is_active', 1);
                    break;
                case 'deactivated':
                    $otts->where('is_active', 0);
                    break;
            }
        }


            $totalFiltered = count($otts->get());
        }

        $otts = $otts
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
        
        //Customize or add additional data in below loop
        $newOtts = [];
        foreach($otts as $key => $ott){
            $activateButton = $ott->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $ott->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="view"> 
                                <a href="javascript;0;"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit"> 
                                <a href="'.route('zq.ott.edit', ['ott' => $ott->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('zq.otts.soft-delete', ['ott' => $ott->id]).'" onclick=" return confirm(\'Are you sure to delete this OTT?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$ott->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';

            $newOtt = [
                'sno' => $key +1,
                'image' => $ott->image_path($ott->image, true),
                'title' => $ott->title,
                'actions' => $actions
            ];
            array_push($newOtts, $newOtt);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newOtts);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $ottId, $action)
    {
        $ott = EntertainmentMasterData::findOrFail($ottId);

        if (!$ott) {
            return response()->json(['error' => 'OTT not found.'], 404);
        }

        if ($action === 'activate') {
            $ott->is_active = 1;
        } elseif ($action === 'deactivate') {
            $ott->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $ott->save();

        return response()->json(['message' => 'OTT status updated successfully.'], 200);
    }
}
