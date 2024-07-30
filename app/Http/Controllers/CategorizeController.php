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
use App\Http\Requests\CategorizeStoreRequest;
use App\Http\Requests\OTTUpdateRequest;
use App\Models\Categorize;

class CategorizeController extends Controller
{
    use TruFlix, HttpResponses;

    protected $categorize;

    public function __construct(
        Categorize $_categorize,
    ){
        $this->categorize = $_categorize;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorizes = $this->categorize->get();
        return view('zq.master-data.categorizes.list', compact('categorizes'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorizes = $this->categorize->get();
        return view('zq.master-data.categorizes.add', compact('categorizes'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategorizeStoreRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();
                $slug = $data['type'].'-'.$this->generateSlug($data['title']);
                $checkSlug = $this->categorize->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                $categorize = $this->categorize->create([
                    'title'             =>  $data['title'],
                    'slug'              =>  $slug,
                    'type'              =>  $data['type'],
                    'is_in_menu'        =>  $data['is_in_menu'] == 1?1:0,
                    'is_show_frontend'  =>  $data['is_show_frontend'] == 1?1:0,
                ]);
                if($categorize){
                    return $this->objectCreated('Categorize details saved successfully.', $categorize);
                }

                return $this->validation('Sorry, Categorize details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.categorizes.index')->with('success', $response->message);
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
    public function edit(Categorize $categorize)
    {
        return view('zq.master-data.categorizes.edit', compact('categorize'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategorizeStoreRequest $request, Categorize $categorize)
    {
        try {
            $response = DB::transaction(function () use ($request, $categorize) {

                $data = $request->all();

                $slug = $data['type'].'-'.$this->generateSlug($data['title']);
                $checkSlug = $this->categorize->whereNot('id', $categorize->id)->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $categorize->title           =   $data['title'];
                $categorize->slug            =   $slug;
                $categorize->type            =   $data['type'];
                $categorize->is_in_menu      =   $data['is_in_menu'] == 1?1:0;
                $categorize->is_show_frontend=   $data['is_show_frontend'] == 1?1:0;

                if($categorize->save()){
                    return $this->objectCreated('Categorize details updated successfully.', $categorize);
                }

                return $this->validation('Sorry, Categorize details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.categorizes.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update categorize: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update categorize: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function softDelete(Categorize $categorize)
    {
        try {
            $response = DB::transaction(function () use ($categorize) {
                if ($categorize->delete()) {
                    return $this->success('Categorize deleted successfully.');
                }

                return $this->validation('Invalid Request to delete categorize.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.categorizes.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

     public function fetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'title',
            'type',
            'is_in_menu',
            'is_show_frontend',
            'id'
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $categorizes = $this->categorize;
        $totalData = count($categorizes->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['status'])){
            //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $categorizes->where('type', 'Like', '%'.$data['search'].'%')
                           ->orWhere('title', 'Like', '%'.$data['search'].'%');
            }

            // Filter by user status (active or deactivated)
            if (!empty($data['status'])) {
                switch ($data['status']) {
                    case 'active':
                        $categorizes->where('is_active', 1);
                        break;
                    case 'deactivated':
                        $categorizes->where('is_active', 0);
                        break;
                }
            }


            $totalFiltered = count($categorizes->get());
        }

        $categorizes = $categorizes
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        foreach($categorizes as $key => $categorize){
            $categorize->sno = $key+1;
            $activateButton = $categorize->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $categorize->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="edit">
                                <a href="'.route('zq.categorizes.edit', ['categorize' => $categorize->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('zq.categorizes.soft-delete', ['categorize' => $categorize->id]).'" onclick=" return confirm(\'Are you sure to delete this Categorize?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$categorize->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';

            $categorize->is_in_menu = $categorize->is_in_menu == 1 ? 'Yes' : 'No';
            $categorize->is_show_frontend = $categorize->is_show_frontend == 1 ? 'Yes' : 'No';
            $categorize->actions = $actions;
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($categorizes);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $categorizeId, $action)
    {
        $categorize = Categorize::findOrFail($categorizeId);

        if (!$categorize) {
            return response()->json(['error' => 'Categorize not found.'], 404);
        }

        if ($action === 'activate') {
            $categorize->is_active = 1;
        } elseif ($action === 'deactivate') {
            $categorize->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $categorize->save();

        return response()->json(['message' => 'Categorize status updated successfully.'], 200);
    }
}
