<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AffiliateRequest;
use App\Http\Requests\AffiliateUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use App\Repositories\CountriesRepository;
class AffiliateController extends Controller
{
    use HttpResponses;

    protected $user;
    protected $userRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        CountriesRepository $countriesRepository

    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->countriesRepository = $countriesRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago


        if(isset($request->id)){
            $user = $this->userRepository->find($request->id);

            $affiliates = $this->userRepository->where('role_id', 5)
                            ->where('country_id', $user->country->id)
                            ->where('is_active', 1)->get(); //Total records

            $newRecords = $this->userRepository->where('role_id', 5)
                        ->where('country_id', $user->country->id)
                        ->where('is_active', 1)
                        ->where('created_at', '>=', $startDate)->get(); // 5 days records

            $deactivatedRecords = $this->userRepository->where('role_id', 5)
                                ->where('country_id', $user->country->id)->where('is_active', 0)->get();
            return view('zq.affiliate.list', compact('affiliates', 'newRecords', 'deactivatedRecords', 'user'));
        }

        $affiliates = $this->userRepository->where('role_id', 5)->where('is_active', 1)->get();
        $newRecords = $this->userRepository->where('role_id', 5)->where('is_active', 1)->where('created_at', '>=', $startDate)->get();
        $deactivatedRecords = $this->userRepository->where('role_id', 5)->where('is_active', 0)->get();
        $countries = $this->countriesRepository->orderBy('name', 'asc')->get();
        return view('zq.affiliate.list', compact('affiliates', 'newRecords', 'deactivatedRecords', 'countries'));
    }

    public function deactivatedList(Request $request)
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago

        if(isset($request->id)){
            $user = $this->userRepository->find($request->id);

            $affiliates = $this->userRepository->where('role_id', 5)
                            ->where('country_id', $user->country->id)
                            ->where('is_active', 1)->get(); //Total records

            $newRecords = $this->userRepository->where('role_id', 5)
                        ->where('country_id', $user->country->id)
                        ->where('is_active', 1)
                        ->where('created_at', '>=', $startDate)->get(); // 5 days records

            $deactivatedRecords = $this->userRepository->where('role_id', 5)
                                ->where('country_id', $user->country->id)->where('is_active', 0)->get();
            return view('zq.affiliate.deactivated_list', compact('affiliates', 'newRecords', 'deactivatedRecords', 'user'));
        }

        $affiliates = $this->userRepository->where('role_id', 5)->where('is_active', 1)->get();
        $newRecords = $this->userRepository->where('role_id', 5)->where('is_active', 1)->where('created_at', '>=', $startDate)->get();
        $deactivatedRecords = $this->userRepository->where('role_id', 5)->where('is_active', 0)->get();
        $countries = $this->countriesRepository->orderBy('name', 'asc')->get();
        return view('zq.affiliate.deactivated_list', compact('affiliates', 'newRecords', 'deactivatedRecords', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = $this->countriesRepository->orderBy('name', 'asc')->get();
        return view('zq.affiliate.add', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AffiliateRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();

                $user = $this->userRepository->where('email', $request->email)->first();
                if($user){
                    $user->access_code = strtoupper($request->access_code);
                    $user->save();
                    return $this->objectCreated('Affiliate added successfully.', $user);
                }

                $data['access_code'] = strtoupper($request->access_code);
                $data['password'] = Hash::make('admin@123!');
                $data['email_verified_at'] = now();
                $data['role_id'] = 5;

                $affiliate = $this->userRepository->create($data);
                if ($affiliate) {
                    return $this->objectCreated('Affiliate added successfully.', $affiliate);
                }

                return $this->validation('Failed to add affiliate details.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.affiliates.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Show Details view
     */
    public function show(User $affiliate){
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentRecords = $this->userRepository->where('role_id', 4)->where('is_active', 1)->where('user_referral_id', $affiliate->id)
                            ->whereDate('created_at', now())->get();
        $currentMonthRecords = $this->userRepository->where('role_id', 4)->where('is_active', 1)->where('user_referral_id', $affiliate->id)
                            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->get();
        $users = $this->userRepository->where('role_id', 4)->where('user_referral_id', $affiliate->id)->where('is_active', 1)->get();
        $deactivatedUsers = $this->userRepository->where('user_referral_id', $affiliate->id)->where('role_id', 4)->where('is_active', 0)->get();

        return view('zq.affiliate.show', compact('affiliate', 'users', 'currentRecords', 'currentMonthRecords', 'deactivatedUsers'));
    }


    /**
     * Show List of users
     */
    public function showUserDetails(Request $request, $affiliate, $type){
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentMonthRecords = $this->userRepository->where('role_id', 4)->where('is_active', 1)
                            ->where('user_referral_id', $affiliate)
                            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->get();
        $users = $this->userRepository->where('role_id', 4)
                            ->where('user_referral_id', $affiliate)
                            ->where('is_active', 1)->get();
        $deactivatedUsers = $this->userRepository->where('role_id', 4)
                            ->where('user_referral_id', $affiliate)
                            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                            ->where('is_active', 0)->get();
        $user = $this->userRepository->find($affiliate);
        return view('zq.affiliate.users_list', compact('currentMonthRecords', 'deactivatedUsers', 'users', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $affiliate)
    {
        $partners = $this->userRepository->where('role_id', 3)->where('is_active', 1)->get();
        $countries = $this->countriesRepository->orderBy('name', 'asc')->get();
        return view('zq.affiliate.edit', compact('affiliate','partners','countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AffiliateUpdateRequest $request, User $affiliate)
    {
        try {
            $response = DB::transaction(function () use ($request, $affiliate) {

                $data = $request->all();

                if($affiliate->email !== $request->email){
                    $checkEmail = $this->userRepository->where('email', $request->email)->where('id', '!=', $affiliate->id)->get()->count();
                    if($checkEmail){
                        return $this->validation('Email is already exists.');
                    }
                }

                if ($affiliate->update($data)) {
                    return $this->success('Affiliate details updated successfully.', $affiliate);
                }

                return $this->validation('Error while updating affiliate details. Please try again later.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.affiliates.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove temp the specified resource from storage.
     */
    public function softDelete(User $affiliate)
    {
        try {
            $response = DB::transaction(function () use ($affiliate) {

                if($affiliate->role_id != 5){
                    return $this->validation('Unable to delete authorized access code.');
                }

                if ($affiliate->update(['is_active' => 0])) {
                    return $this->success('Affiliate deleted successfully.');
                }

                return $this->validation('Invalid Request to delete affiliate.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.affiliates.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Fetch Data for DataTable Ajax
     */
    public function fetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'email',
            'name',
            'created_at',
            'id',
            'id',
            'id',
            'access_code',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $affiliates = $this->userRepository->where(function($query) use ($data){

            if(isset($data['status']) && in_array($data['status'], [0, 1])){
                $query->where('is_active', $data['status']);
            }elseif(isset($data['request_data']) && $data['request_data'] === 'deactivated_users'){
                $query->whereIn('is_active', 0);
            }else{
                $query->whereIn('is_active', [0, 1]);
            }

            $query->where('role_id', 5);
        });




        if(isset($data['country_id']) && !empty($data['country_id'])){
            // $user = $this->userRepository->find($request->id);
            $affiliates->where('country_id', $data['country_id']);
        }

        $totalData = count($affiliates->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $affiliates->where('access_code', 'Like', '%'.$data['search'].'%')
                           ->orWhere('email', 'Like', '%'.$data['search'].'%')
                           ->orWhere('name', 'Like', '%'.$data['search'].'%');
            }

            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $affiliates->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $affiliates->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $affiliates->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $affiliates->whereDate('created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }

            // Filter by user status (active or deactivated)
            if (!empty($data['status'])) {
                switch ($data['status']) {
                    case 'active':
                        $affiliates->where('is_active', 1);
                        break;
                    case 'deactivated':
                        $affiliates->where('is_active', 0);
                        break;
                }
            }

            $totalFiltered = count($affiliates->get());
        }

        $affiliates = $affiliates
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newAffiliates = [];
        foreach($affiliates as $key => $affiliate){

            // <li class="delete"><a href="'.route('zq.affiliates.soft-delete', ['affiliate' => $affiliate->id]).'" onclick=" return confirm(\'Are you sure to delete affiliate?\');"><i class="icon-trash"></i></a></li>

            if($data['status'] === 'active'){
                $actions = '<ul class="action align-center">
                                <li class="view">
                                    <a href="'.route('zq.affiliates.show', ['affiliate' => $affiliate->id]).'"><i class="icon-eye"></i></a>
                                </li>
                                <li class="edit">
                                    <a href="'.route('zq.affiliates.edit', ['affiliate' => $affiliate->id]).'">
                                        <i class="icon-pencil-alt"></i></a>
                                </li>

                                <li class="activate">
                                    <a href="javascript:void(0);" class="btn-activate" data-id="'.$affiliate->id.'" data-action="'.($affiliate->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($affiliate->is_active ? 'Deactivate' : 'Activate').' User" >
                                        '.($affiliate->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
                                    </a>
                                </li>
                            </ul>';
            }else{
                $actions = '<ul class="action align-center">
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$affiliate->id.'" data-action="'.($affiliate->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($affiliate->is_active ? 'Deactivate' : 'Activate').' User" >
                                    '.($affiliate->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
                                </a>
                            </li>
                        </ul>';
            }

            $newAffiliate = [
                'sno' => $key +1,
                'email' => $affiliate->email,
                'name' => $affiliate->name,
                'country' => $affiliate->country->name ?? '',
                'joined' => date('M d, Y', strtotime($affiliate->created_at)),
                'referred' => count($affiliate->affiliates),
                'status' => 0,
                'access_code' => strtoupper($affiliate->access_code),
                'actions' => $actions
            ];
            array_push($newAffiliates, $newAffiliate);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newAffiliates);
        return response()->json($response, 200);
    }

    /**
     * Users Fetch Data for DataTable Ajax
     */
    public function fetchAffiliateUsersListForAjax(Request $request){

        $toDate = Carbon::now();
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentUser = app('truFlix')->getSessionUser();

        //Defined Columns
        $columnArray = [
            'id',
            'email',
            'name',
            'created_at',
            'id',
            'id',
            'id',
            'access_code',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $is_active = 1;
        if(isset($data['requestType']) && $data['requestType'] == 'deactivated'){
            $is_active = 0;
        }

        $users = $this->userRepository->where(function($query) use ($is_active, $data){
            $query->where('is_active', $is_active)
            ->where('role_id', 4)
            ->where('user_referral_id', $data['id']);
        });


        if(isset($data['requestType']) && $data['requestType'] == 'current-date'){
            $users->whereDate('created_at', $toDate);
        }

        if(isset($data['requestType']) && $data['requestType'] == 'current-month'){
            $users->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
        }

        $totalData = count($users->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $users->where(function($query) use ($data){
                    $query->where('access_code', 'Like', '%'.$data['search'].'%')
                            ->orWhere('email', 'Like', '%'.$data['search'].'%')
                            ->orWhere('name', 'Like', '%'.$data['search'].'%');
                });
            }
            $totalFiltered = count($users->get());
        }

        $affiliatedUsers = $users
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newAffiliatedUsers = [];
        foreach($affiliatedUsers as $key => $user){

            $actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="'.route('default.users.details', ['user' => $user->id]).'"><i class="icon-eye"></i></a>
                            </li>
                        </ul>';

            $newAffiliatedUser = [
                'sno' => $key +1,
                'email' => $user->email,
                'name' => $user->name,
                'country' => $user->country->name ?? '',
                'joined' => date('M d, Y', strtotime($user->created_at)),
                'referred' => count($user->affiliates),
                'status' => 0,
                'access_code' => strtoupper($user->access_code),
                'actions' => $actions
            ];
            array_push($newAffiliatedUsers, $newAffiliatedUser);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newAffiliatedUsers);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $adminId, $action)
    {
        $user = User::findOrFail($adminId);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($action === 'activate') {
            $user->is_active = 1;
        } elseif ($action === 'deactivate') {
            $user->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $user->save();

        return response()->json(['message' => 'User status updated successfully.'], 200);
    }


}
