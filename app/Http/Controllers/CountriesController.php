<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use Illuminate\Http\Request;
use App\Repositories\CountriesRepository;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\CountryUpdateRequest;

class CountriesController extends Controller
{
    use TruFlix, HttpResponses;

    protected $countries;

    public function __construct(
        CountriesRepository $_countries,
    ){
        $this->countries = $_countries;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('zq.master-data.countries.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('zq.master-data.countries.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        try {

            $response = DB::transaction(function () use ($request) {
                $data = $request->all();

                $country = $this->countries->create([
                    'name' => $data['name'],
                    'code' => 0
                ]);
                if($country){
                    return $this->objectCreated('Country details saved successfully.', $country);
                }

                return $this->validation('Sorry, Country details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.countries.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Countries $countries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Countries $country)
    {
        return view('zq.master-data.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryUpdateRequest $request, Countries $country)
    {
        try {
            $response = DB::transaction(function () use ($request, $country) {

                $data = $request->all();

                //Map Data to save details
                $country->name           =   $data['name'];

                if($country->save()){

                    return $this->objectCreated('Country details updated successfully.', $country);
                }

                return $this->validation('Sorry, Country details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.countries.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            Log::error('Failed to update country: ' . $th->getMessage());
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

    /**
     * Remove the specified resource from storage.
     */
    public function softDelete(Countries $country)
    {
        try {
            $response = DB::transaction(function () use ($country) {
                if ($country->delete()) {
                    return $this->success('Genre deleted successfully.');
                }

                return $this->validation('Invalid Request to delete Genre.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.countries.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function fetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'name',
            'name',
            'name',
        ];

        $data = $request->all();

        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $countries = $this->countries;


        $totalData = count($countries->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search'])){ //.... conduction's can add in this if clause

            $countries->where('name', 'Like', '%'.$data['search'].'%');

            $totalFiltered = count($countries->get());
        }

        $countries = $countries
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        foreach($countries as $key => $country){
            $activateButton = $country->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $country->is_active ? 'deactivate' : 'activate';
            $country->actions = '<ul class="action align-center">
                            <li class="edit">
                                <a href="'.route('zq.countries.edit', ['country' => $country->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>

                            <li class="activate">
                                <a href="javascript:void(0);" data-url="'.route('zq.countries.update.status', ['action'=> $activateAction, 'country' => $country->id]).'" class="btn-activate" data-id="'.$country->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';
            $country->sno = $key + 1;
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($countries);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, Countries $country, $action){
        // $country = $this->countries->findOrFail($country);

        if (!$country) {
            return response()->json(['error' => 'genre not found.'], 404);
        }

        if ($action === 'activate') {
            $country->is_active = 1;
        } elseif ($action === 'deactivate') {
            $country->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $country->save();

        return response()->json(['message' => 'genre status updated successfully.'], 200);
    }
}
