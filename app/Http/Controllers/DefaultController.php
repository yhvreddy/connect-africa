<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserRegisterUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\UserSubscriptionsHistoryRepository;

class DefaultController extends Controller
{
    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        UserSubscriptionsRepository $_userSubscriptions,
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->userSubscriptions = $_userSubscriptions;
        $this->userSubscriptionHistory = $_userSubscriptionHistory;
    }

    /**
     * User Details Page
     */
    public function userDetails(Request $request, User $user){
        $currentUser = app('truFlix')->getSessionUser();
        $userParent = $this->userRepository->where('id', $user->user_referral_id)->first();
        $userParentReferrals = [];
        if($userParent){
            $userParentReferrals[] = $userParent;
            foreach($userParent->referredUsers as $key => $referredUser){
                if(in_array($referredUser->role_id, [4,5])){
                    $userParentReferrals[] = $referredUser;
                }
            }
        }
        $userParentReferrals = collect($userParentReferrals);

        $referredUsers = $user->referredUsers;
        return view('default.users.view', compact('user', 'referredUsers', 'userParentReferrals', 'currentUser'));
    }

    /**
     *
     */
    public function affiliateDetailView(Request $request, User $affiliate){
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

        return view('default.affiliate.show', compact('affiliate', 'users', 'currentRecords', 'currentMonthRecords', 'deactivatedUsers'));
    }

    /**
     * Show List of users
     */
    public function showUserDetails(Request $request, $affiliate, $type){
        $currentDate = Carbon::now();
        $startDate = $currentDate->subDays(5)->startOfDay(); // Calculate the date 5 days ago
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentMonthRecords = $this->userRepository->where('role_id', 4)
                            ->where('is_active', 1)
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
        return view('default.affiliate.users_list', compact('currentMonthRecords', 'deactivatedUsers', 'users', 'user'));
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

            $actions = '<div class="view text-center">
                            <a href="'.route('default.users.details', ['user' => $user->id]).'"><i class="icon-eye"></i></a>
                        </div>';

            $newAffiliatedUser = [
                'sno' => $key +1,
                'email' => $user->email,
                'name' => $user->name,
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

    // Territory
    public function territoryDetails(User $territory){
        $territoryUsers = $this->userRepository->where('role_id', 4)
                        ->where('is_active', 1)
                        ->where('country_id', $territory->country->id)
                        ->get();
        $affiliates = $this->userRepository->where('role_id', 5)
                        ->where('is_active', 1)->where('country_id', $territory->country->id)->get();
        $deactivated = $this->userRepository->where('role_id', 4)
                        ->where('is_active', 0)
                        ->where('country_id', $territory->country->id)
                        ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                        ->get();
        $newRecords = $this->userRepository->where('role_id', 4)
                        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                        ->where('is_active', 0)->where('country_id', $territory->country->id)->get();

        $monthEarnings = $this->userSubscriptionHistory->leftJoin('users', 'users.id', 'users_subscriptions_history.user_id')
                        ->where('users.country_id', $territory->country->id)
                        ->whereIn('users_subscriptions_history.type', ['invoice'])->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month)->get();
        $monthEarningAmount = 0;
        foreach ($monthEarnings as $key => $monthEarning) {
            $amount = json_decode($monthEarning->response_data)->amount_paid ?? 0.0;
            $monthEarningAmount += number_format($amount / 100, 2);
        }
        $user = $this->userRepository->find($territory->id);
        return view('default.territory.show', compact('territory', 'territoryUsers', 'affiliates', 'deactivated', 'newRecords', 'monthEarningAmount', 'user'));
    }
}
