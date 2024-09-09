<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\PartnerRegisterRequest;
use App\Http\Requests\ProfilePartnerUpdateRequest;
use App\Http\Requests\PartnerRegisterUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use App\Repositories\CountriesRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class PartnerController extends Controller
{
    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;
    protected $countryRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        CountriesRepository $countryRepository
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $newRecords = $this->userRepository->where('role_id', 3)
            ->where('is_active', 1)
            ->where('updated_at', '>=', $startDate)
            ->get(); // 5 days records
        $endDate = Carbon::now()->endOfMonth();
        $deactivatedRecords = $this->userRepository
            ->where('role_id', 3)
            ->where('is_active', 0)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();
        $partners = $this->userRepository->where('role_id', 3)
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return view('zq.partner.list', compact('partners','startDate','newRecords','deactivatedRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = $this->countryRepository->where('is_active', 1)->get();
        return view('zq.partner.add',  compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartnerRegisterRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();

                //Check Country and Get Id or Insert as new
                $country = $this->countryRepository->find($data['country_id']);
                if (!$country) {
                    return $this->validation('Invalid territory.');
                }

                $data['password'] = Hash::make($data['password'] ?? 'admin@123!');
                $data['username'] = $data['email'];
                $data['role_id'] = 3;
                $data['email_verified_at'] = now();
                $data['country_id'] = $country->id;
                $data['access_code'] = $this->generateRandomCode(6);
                $savePartner = $this->userRepository->create($data);
                if ($savePartner) {
                    return $this->objectCreated('Partner added successfully.', $savePartner);
                }

                return $this->validation('Failed to add partner details.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.partners.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $partner)
    {
        $countries = $this->countryRepository->where('is_active', 1)->get();
        return view('zq.partner.edit', compact('partner', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartnerRegisterUpdateRequest $request, User $partner)
    {
        try {
            $response = DB::transaction(function () use ($request, $partner) {
                if($partner->email !== $request->email){
                    $checkEmail = $this->userRepository->where('email', $request->email)->where('id', '!=', $partner->id)->get()->count();
                    if($checkEmail){
                        return $this->validation('Email is already exists.');
                    }
                }

                $data = $request->all();


                //Check Country and Get Id or Insert as new
                $country = $this->countryRepository->find($data['country_id']);
                if(!$country){
                    return $this->validation('Invalid territory.');
                }

                $newData = [];
                $data['country_id'] = $country->id;
                $data['username'] = $data['email'];
                if(!empty($data['password']) && isset($data['password'])){
                    $newData['password'] = Hash::make($data['password']);
                }else{
                    unset($data['password']);
                }

                $data = array_merge($data, $newData);
                if ($partner->update($data)) {
                    return $this->success('Partner details updated successfully.', $partner);
                }

                return $this->validation('Error while updating partner details. Please try again later.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.partners.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Remove temp the specified resource from storage.
     */
    public function softDelete(User $partner)
    {
        try {
            $response = DB::transaction(function () use ($partner) {
                $partner->is_active = 0;
                if ($partner->save()) {
                    // $partner->delete();
                    return $this->success('Admin deleted successfully.');
                }

                return $this->validation('Invalid Request to delete admin.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.partners.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function profileEdit(){
        $user = app('truFlix')->getSessionUser();
        return view('partner.profile.index', compact('user'));
    }

    public function profileUpdate(User $partner, ProfilePartnerUpdateRequest $request){
        try {
            $response = DB::transaction(function () use ($request, $partner) {
                if($partner->email !== $request->email){
                    $checkEmail = $this->user->where('email', $request->email)->where('id', '!=', $partner->id)->get()->count();
                    if($checkEmail){
                        return $this->validation('Email is already exists.');
                    }
                }

                $data = $request->all();
                $newData = [];
                if(!empty($data['password']) && isset($data['password'])){
                    $newData['password'] = Hash::make($data['password']);
                }else{
                    unset($data['password']);
                }

                $data = array_merge($data, $newData);
                if ($partner->update($data)) {
                    return $this->success('Profile details updated successfully.', $partner);
                }

                return $this->validation('Error while updating profile details. Please try again later.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->back()->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Territory users
     */
    public function territoryUsers(Request $request){
        $currentUser = app('truFlix')->getSessionUser();

        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentMonthRecords = $this->userRepository->where('role_id', 4)->whereIn('is_active', [0, 1])
                            ->where('user_referral_id', $currentUser->id)
                            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->get();
        $users = $this->userRepository->where('role_id', 4)
                            ->where('user_referral_id', $currentUser->id)
                            ->whereIn('is_active', [0, 1])->get();
        $deactivatedUsers = $this->userRepository->where('role_id', 4)
                            ->where('user_referral_id', $currentUser->id)
                            ->whereBetween('updated_at', [$currentMonthStart, $currentMonthEnd])
                            ->where('is_active', 0)->get();

        return view('partner.territory.users_list', compact('currentMonthRecords', 'deactivatedUsers', 'users'));
    }

    /**
     * Territory User List By Ajax
     */
    public function fetchTerritoryUsersListForAjax(Request $request){
        $currentUser = app('truFlix')->getSessionUser();

        $toDate = Carbon::now();
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        //Defined Columns
        $columnArray = [
            'id',
            'email',
            'email',
            'name',
            'created_at',
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

        $users = $this->userRepository->where(function($query) use ($data, $currentUser){

            if(isset($data['status']) && in_array($data['status'], [0, 1])){
                $query->where('is_active', $data['status']);
            }elseif(isset($data['request_data']) && $data['request_data'] === 'deactivated_users'){
                $query->whereIn('is_active', 0);
            }else{
                $query->whereIn('is_active', [0, 1]);
            }

            $query->where('role_id', 4)
            ->where('user_referral_id', $currentUser->id);

        });

        if(isset($data['requestType']) && $data['requestType'] == 'current-date') $users->whereDate('created_at', $toDate);
        if(isset($data['requestType']) && $data['requestType'] == 'current-month') $users->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);

        if(isset($data['request_data']) && $data['request_data'] === 'new_users') $users->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

        $totalData = count($users->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $users->where(function($query) use ($data){
                    $query->where('access_code', 'Like', '%'.$data['search'].'%')
                            ->orWhere('email', 'Like', '%'.$data['search'].'%')
                            ->orWhere('name', 'Like', '%'.$data['search'].'%');
                });
            }


            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $users->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $users->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $users->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $users->whereDate('created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }


            $totalFiltered = count($users->get());
        }

        $territoryUsers = $users
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newTerritoryUsers = [];
        foreach($territoryUsers as $key => $user){
            $actions = '<ul class="text-center view">
                            <a href="'.route('default.users.details', ['user' => $user->id]).'"><i class="icon-eye"></i></a>
                        </ul>';

            $newTerritoryUser = [
                'sno' => $key +1,
                'email' => $user->email,
                'name' => $user->name,
                'joined' => date('M d, Y', strtotime($user->created_at)),
                'referred' => count($user->affiliates),
                'status' => $user->is_active === 1?'Active':'Deactivated',
                'access_code' => strtoupper($user->access_code),
                'action' => $actions
            ];
            array_push($newTerritoryUsers, $newTerritoryUser);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newTerritoryUsers);
        return response()->json($response, 200);
    }

    /**
     * Affiliate users list
     */
    public function affiliates(Request $request){
        $user = app('truFlix')->getSessionUser();
        return view('partner.affiliates.index', compact('user'));
    }

    /**
     * Affiliate users list fetch ajax
     */
    public function affiliateListForAjax(Request $request){
        $currentUser = app('truFlix')->getSessionUser();

        $toDate = Carbon::now();
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        //Defined Columns
        $columnArray = [
            'id',
            'name',
            'created_at',
            'users',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $users = $this->userRepository->where(function($query) use ($data, $currentUser){
            if((isset($data['status']) && !empty($data['status']))
                && $data['status'] !== 'all'){
                $query->where('is_active', $data['status']);
            }

            $query->where('role_id', 5)
            ->where('country_id', $currentUser->country->id);
        });

        $totalData = count($users->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $users->where(function($query) use ($data){
                    $query->where('access_code', 'Like', '%'.$data['search'].'%')
                            ->orWhere('email', 'Like', '%'.$data['search'].'%')
                            ->orWhere('name', 'Like', '%'.$data['search'].'%');
                });
            }


            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $users->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $users->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $users->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $users->whereDate('created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }


            $totalFiltered = count($users->get());
        }

        $territoryUsers = $users
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newTerritoryUsers = [];
        foreach($territoryUsers as $key => $user){
            $actions = '<div class="view text-center">
                            <a href="'.route('default.affiliates.show', ['affiliate' => $user->id]).'"><i class="icon-eye"></i></a>
                        </div>';

            $newTerritoryUser = [
                'sno' => $key +1,
                'name' => $user->name,
                'joined' => date('M d, Y', strtotime($user->created_at)),
                'users' => count($user->getAffiliatesReferralByCountry($user->id, $user->country->id)),
                'action' => $actions
            ];

            array_push($newTerritoryUsers, $newTerritoryUser);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newTerritoryUsers);
        return response()->json($response, 200);
    }

    public function partnerFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'country_name',
            'name',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $partners = $this->userRepository->where('role_id', 3)
                ->orderBy('id', 'DESC');

        if (!empty($data['territory'])) {
            $partners->where('country_id', $data['territory']);
        }

        // Filter by user status (active or deactivated)
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 'active':
                    $partners->where('is_active', 1);
                    break;
                case 'deactivated':
                    $partners->where('is_active', 0);
                    break;
            }
        }

        $totalData = count($partners->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $partners->where('role_id', 'Like', '%'.$data['search'].'%')
                           ->orWhere('name', 'Like', '%'.$data['search'].'%')
                           ->orWhereHas('country', function ($query) use ($data) {
                            $query->where('name', 'like', '%' . $data['search'] . '%');
                        });

            }

            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $partners->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $partners->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $partners->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $partners->whereDate('created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }

            $totalFiltered = count($partners->get());
        }

        $partners = $partners
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newPartners = [];
        foreach($partners as $key => $partner){

            //  <li class="delete"><a href="'.route('zq.partners.soft-delete', ['partner' => $partner->id]).'" onclick=" return confirm(\'Are you sure to delete this partner?\');"><i class="icon-trash"></i></a></li>

            $activateButton = $partner->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $partner->is_active ? 'deactivate' : 'activate';


            if($data['status'] === 'active'){
                $actions = '<ul class="action align-center">
                                <li class="view">
                                    <a href="'.route('default.territory', ['territory' => $partner->id]).'"><i class="icon-eye"></i></a>
                                </li>
                                <li class="edit">
                                    <a href="'.route('zq.partners.edit', ['partner' => $partner->id]).'"><i class="icon-pencil-alt"></i></a>
                                </li>

                                <li class="activate">
                                    <a href="javascript:void(0);" class="btn-activate" data-id="'.$partner->id.'" data-action="'.($partner->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($partner->is_active ? 'Deactivate' : 'Activate').' User" >
                                        '.($partner->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
                                    </a>
                                </li>
                            </ul>';
            }else{
                $actions = '<ul class="action align-center">
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$partner->id.'" data-action="'.($partner->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($partner->is_active ? 'Deactivate' : 'Activate').' User" >
                                    '.($partner->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
                                </a>
                            </li>
                        </ul>';
            }

            $active_users = $this->userRepository->where('user_referral_id', $partner->id)->whereNot('role_id', 3)->where('is_active', 1)->get();

            $newPartner = [
                'sno' => $key +1,
                'country_name' => $partner->country->name ?? '---',
                'name' => $partner->name,
                'email' => $partner->email,
                'active_users' => $partner->referredUsers->count(),
                'actions' => $actions
            ];

            array_push($newPartners, $newPartner);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newPartners);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $partnerId, $action)
    {
        $partner = User::findOrFail($partnerId);

        if (!$partner) {
            return response()->json(['error' => 'partner not found.'], 404);
        }

        if ($action === 'activate') {
            $partner->is_active = 1;
        } elseif ($action === 'deactivate') {
            $partner->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $partner->save();

        return response()->json(['message' => 'Partner status updated successfully.'], 200);
    }


    public function deactivatedList(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $newRecords = $this->userRepository->where('role_id', 3)->where('is_active', 1)
        ->where('updated_at', '>=', $startDate)->get(); // 5 days records
        $endDate = Carbon::now()->endOfMonth();
        $deactivatedRecords = $this->userRepository
        ->where('role_id', 3)
        ->where('is_active', 0)
        ->whereBetween('updated_at', [$startDate, $endDate])
        ->get();

        $partners = $this->userRepository->where('role_id', 3)
            ->orderBy('id', 'DESC')->get();
        $deactivatePartners = $this->userRepository->where('role_id', 3)
            ->where('is_active', 0)
            ->orderBy('id', 'DESC')->get();
        return view('zq.partner.deactivated_list', compact('partners','startDate','newRecords','deactivatedRecords', 'deactivatePartners'));
    }
}
