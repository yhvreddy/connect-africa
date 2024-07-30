<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterUpdateRequest;
use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\UserStripeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use App\Repositories\CountriesRepository;
use Illuminate\Support\Carbon;


class PaymentController extends Controller
{

    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;
    protected $paymentRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        CountriesRepository $countryRepository,
        UserSubscriptionsRepository $_userSubscriptions,
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory,
        UserStripeRepository $_userStripe,
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
        $this->userSubscriptions = $_userSubscriptions;
        $this->userSubscriptionHistory = $_userSubscriptionHistory;


        $this->stripe = new \Stripe\StripeClient(config('app.stripe_secret'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = $this->countryRepository->all();
        $country_id = request()->country_id ?? null;
        $yearEarnings = $this->userSubscriptionHistory->leftJoin('users','users_subscriptions_history.user_id', 'users.id');
        if(!empty($country_id)){
            $yearEarnings->where('users.country_id', $country_id);
        }
        $yearEarnings = $yearEarnings->whereIn('users_subscriptions_history.type', ['invoice'])
            ->whereYear('users_subscriptions_history.created_at',Carbon::now()->year)
            ->get();
        $yearEarningAmount = 0;
        foreach ($yearEarnings as $key => $yearEarning) {
            $amount = json_decode($yearEarning->response_data)->amount_paid ?? 0.0;
            $yearEarningAmount += number_format($amount / 100, 2);
        }

        $monthEarnings = $this->userSubscriptionHistory->leftJoin('users','users_subscriptions_history.user_id', 'users.id');
            if(!empty($country_id)){
                $monthEarnings->where('users.country_id', $country_id);
            }
        $monthEarnings = $monthEarnings->whereIn('users_subscriptions_history.type', ['invoice'])->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month)->get();
        $monthEarningAmount = 0;
        foreach ($monthEarnings as $key => $monthEarning) {
            $amount = json_decode($monthEarning->response_data)->amount_paid ?? 0.0;
            $monthEarningAmount += number_format($amount / 100, 2);
        }

        $monthTransactions = $this->userSubscriptionHistory->leftJoin('users','users_subscriptions_history.user_id', 'users.id');
            if(!empty($country_id)){
                $monthTransactions->where('users.country_id', $country_id);
            }
        $monthTransactions = $monthTransactions->whereIn('users_subscriptions_history.type',['invoice'])->whereMonth('users_subscriptions_history.created_at',Carbon::now()->month)->get();

        $transactionStatus = $this->userSubscriptionHistory->leftJoin('users','users_subscriptions_history.user_id', 'users.id');
            if(!empty($country_id)){
                $transactionStatus->where('users.country_id', $country_id);
            }
        $transactionStatus = $transactionStatus->select('subscription_status')
                ->groupBy('subscription_status')
                ->get();

        return view('zq.payment.index', compact('yearEarningAmount', 'monthEarningAmount', 'monthTransactions', 'countries'));
    }


    public function partnersPayments()
    {
        $currentUser = app('truFlix')->getSessionUser();

        $countries = $this->countryRepository->all();
        $yearEarnings = $this->userSubscriptionHistory
                        ->leftJoin('users','users_subscriptions_history.user_id', 'users.id')
                        ->select('users_subscriptions_history.*')
                        ->where('users.user_referral_id', $currentUser->id)
                        ->whereIn('type', ['invoice'])
                        ->whereYear('users_subscriptions_history.created_at',Carbon::now()->year)
                        ->get();
        $yearEarningAmount = 0;
        foreach ($yearEarnings as $key => $yearEarning) {
            $amount = json_decode($yearEarning->response_data)->amount_paid ?? 0.0;
            $yearEarningAmount += number_format($amount / 100, 2);
        }

        $monthEarnings = $this->userSubscriptionHistory
                        ->leftJoin('users','users_subscriptions_history.user_id', 'users.id')
                        ->select('users_subscriptions_history.*')
                        ->where('users.user_referral_id', $currentUser->id)
                        ->whereIn('type', ['invoice'])
                        ->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month)
                        ->get();
        $monthEarningAmount = 0;
        foreach ($monthEarnings as $key => $monthEarning) {
            $amount = json_decode($monthEarning->response_data)->amount_paid ?? 0.0;
            $monthEarningAmount += number_format($amount / 100, 2);
        }

        $monthTransactions = $this->userSubscriptionHistory
                            ->leftJoin('users','users_subscriptions_history.user_id', 'users.id')
                            ->select('users_subscriptions_history.*')
                            ->where('users.user_referral_id', $currentUser->id)
                            ->whereIn('type',['invoice'])
                            ->whereMonth('users_subscriptions_history.created_at',Carbon::now()->month)->get();

        $transactionStatus = $this->userSubscriptionHistory->select('subscription_status')->groupBy('subscription_status')->get();
        $request_data = request()->request_data ?? 'yearly';
        return view('partner.payments.index', compact('yearEarningAmount', 'monthEarningAmount', 'monthTransactions', 'countries', 'request_data'));
    }


    public function fetchTransactionsList(Request $request){

        $currentUser = app('truFlix')->getSessionUser();

        // Defined Columns
        $columnArray = [
            'id',
            'name',
            'email',
            'created_at',
            'stripe_customer_id',
            'status',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $userSubscription = $this->userSubscriptionHistory->whereIn('users_subscriptions_history.type', ['invoice'])
                    ->leftJoin('users', 'users_subscriptions_history.user_id', 'users.id')
                    ->select('users_subscriptions_history.*'); // Initialize $users here
        if(isset($request->type)){
            if($request->type === 'partner'){
                $userSubscription->where('users.user_referral_id', $currentUser->id);
            }
        }

        if(isset($request->request_data)){
            if($request->request_data === 'monthly'){
                $userSubscription->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month);
            }
        }

        $totalData = $userSubscription->count();
        $totalFiltered = $totalData;

        // Filter Data
        if (!empty($data['username']) || !empty($data['payment_id']) || !empty($data['duration']) || !empty($data['country_id'])) {

            if (!empty($data['username'])) {
                $userSubscription->where(function($query) use ($data) {
                    $query->where('users.name', 'LIKE', '%'.$data['username'].'%');
                });
            }

            if (!empty($data['payment_id'])) {
                $userSubscription->where(function($query) use ($data) {
                    $query->where('users_subscriptions_history.response_data', 'LIKE', '%'.$data['payment_id'].'%');
                });
            }

            if (!empty($data['country_id'])) {
                $userSubscription->where(function($query) use ($data) {
                    $query->where('users.country_id', $data['country_id']);
                });
            }

            if (!empty($data['duration'])) {
                switch ($data['duration']) {
                    case 'current_week':
                        $userSubscription->whereBetween('users_subscriptions_history.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'current_month':
                        $userSubscription->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month);
                        break;
                    case 'current_year':
                        $userSubscription->whereYear('users_subscriptions_history.created_at', Carbon::now()->year);
                        break;
                    case 'today':
                        $userSubscription->whereDate('users_subscriptions_history.created_at', Carbon::today());
                        break;
                    case 'all_time':
                        // No need to apply additional filter
                        break;
                }
            }

            $totalFiltered = $userSubscription->count();
        }


        $userSubscriptions = $userSubscription
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        foreach ($userSubscriptions as $key => $userSubscription) {
            $userSubscription->user_name    =   $userSubscription->user->name;
            $subscriptionPayment = json_decode($userSubscription->response_data);

            $invoicePDF = $subscriptionPayment->invoice_pdf;

            $userSubscription->response_data = $subscriptionPayment;
            $userSubscription->actions = '<ul class="action align-center">
                            <li class="view">
                                <a href="'.$invoicePDF.'" target="_blank" data-toggle="tooltip" data-placement="top" title="Invoice" ><i class="icon-eye"></i></a>
                            </li>
                        </ul>';

            $userSubscription->created_date = date('M d, Y', strtotime($userSubscription->created_at));
            $userSubscription->sno  = $userSubscription->id;
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = $userSubscriptions;
        return response()->json($response, 200);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
