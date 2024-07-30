<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\TruFlix;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Repositories\UserRepository;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\EntertainmentRepository;
use App\Repositories\CategoriesRepository;
use Illuminate\Support\Carbon;
use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\CountriesRepository;

class IndexController extends Controller
{
    use TruFlix, HttpResponses;

    protected $user;
    protected $userRepository;
    protected $emdRepository;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository,
        UserSubscriptionsRepository $_userSubscriptions,
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory,
        CountriesRepository $countryRepository,
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->emdRepository = $_entertainmentMasterDataRepository;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
        $this->userSubscriptions = $_userSubscriptions;
        $this->userSubscriptionHistory = $_userSubscriptionHistory;
        $this->countryRepository = $countryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = $this->getSessionUser();
        if($user){
            return redirect()->back();
        }

        return view('auth.login');
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

    //Dashboard Controllers
    //ZQ (Super-admin) Dashboard
    public function zqDashboard(){
        $user = app('truFlix')->getSessionUser();
        $admins = $this->user->where('role_id', 2)->get()->count();
        $partners = $this->user->where('role_id', 3)->get()->count();
        $users = $this->user->where('role_id', 4)->get()->count();
        $deactivatedUsers = $this->user->where('role_id', 4)->where('is_active', 0)->get()->count();
        $affiliates = $this->user->where('role_id', 5)->get()->count();

        $countries = $this->countryRepository
                    // ->leftJoin('users', 'users.country_id', 'countries.id')
                    ->select('countries.id', 'countries.name', 'countries.code')
                    ->where('countries.is_active', 1)
                    // ->groupBy('users.country_id')
                    ->get();

        //Earnings
        $totalEarningsThisYear = $this->userSubscriptionHistory->whereIn('type', ['invoice'])
                                ->whereYear('created_at',Carbon::now()->year)->get();
        $totalEarningsOfYearAmount = 0;
        foreach ($totalEarningsThisYear as $key => $value) {
            $amount = json_decode($value->response_data)->amount_paid ?? 0;
            $totalEarningsOfYearAmount += number_format($amount / 100, 2);
        }

        $totalEarningsThisMonth = $this->userSubscriptionHistory->whereIn('type', ['invoice'])->whereMonth('created_at', Carbon::now()->month)->get();
        $totalEarningsOfMonthAmount = 0;
        foreach ($totalEarningsThisMonth as $key => $value) {
            $amount = json_decode($value->response_data)->amount_paid ?? 0;
            $totalEarningsOfMonthAmount += number_format($amount / 100, 2);
        }

        $monthTransactions = $this->userSubscriptionHistory->whereIn('type',['invoice'])->whereMonth('created_at',Carbon::now()->month)->get();
        return view('zq.index', compact('admins', 'partners', 'users', 'affiliates', 'deactivatedUsers', 'totalEarningsOfYearAmount','totalEarningsOfMonthAmount', 'countries'));
    }

    //Admin Dashboard
    public function adminDashboard(){

        $user = app('truFlix')->getSessionUser();
        $users = $this->user->where('role_id', 4)->get()->count();
        //Categories
        $categoryMovies = $this->categoryRepository->where('slug', 'movies')->first();
        $categoryShows = $this->categoryRepository->where('slug', 'shows')->first();
        $categoryEvents = $this->categoryRepository->where('slug', 'events')->first();

        //Total
        $allCount = $this->entertainmentRepository->count();
        $adminAllCount = $this->entertainmentRepository->where('user_id', $user->id)->count();

        $moviesCount = $this->entertainmentRepository
                        ->where('category_id', $categoryMovies->id)
                        ->count();

        $adminMoviesCount = $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->where('user_id', $user->id)
                            ->count();

        $showsCount = $this->entertainmentRepository->where('category_id', $categoryShows->id)->count();
        $adminShowsCount = $this->entertainmentRepository
                           ->where('category_id', $categoryShows->id)
                           ->where('user_id', $user->id)
                           ->count();

        $eventsCount = $this->entertainmentRepository->where('category_id', $categoryEvents->id)->count();
        $adminEventsCount = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->where('user_id', $user->id)
                            ->count();

        //overview of month
        $currentDate = Carbon::now();
        $startDateThisWeek = $currentDate->startOfWeek();
        $startDate = $currentDate->subDays(5)->startOfDay();
        $startDateToday = Carbon::today();

        $moviesCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();
        $adminMoviesCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('user_id', $user->id)
                            ->count();

        $showsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryShows->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();
        $adminShowsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryShows->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('user_id', $user->id)
                            ->count();

        $eventsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();
        $adminEventsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('user_id', $user->id)
                            ->count();

        $totalCountMonth = $moviesCountMonth + $showsCountMonth + $eventsCountMonth;
        $adminTotalCountMonth = $adminMoviesCountMonth + $adminShowsCountMonth + $adminEventsCountMonth;

        return view('admin.index', compact('users','user','allCount','moviesCount','showsCount','eventsCount','moviesCountMonth','showsCountMonth','eventsCountMonth','totalCountMonth', 'adminMoviesCount', 'adminShowsCount', 'adminEventsCount', 'adminAllCount', 'adminTotalCountMonth', 'adminEventsCountMonth', 'adminShowsCountMonth', 'adminMoviesCountMonth'));
    }

    //Partner Dashboard
    public function partnerDashboard(){
        $user = app('truFlix')->getSessionUser();
        $totalUsers = $this->user->where('role_id', 4)->where('country_id', $user->country_id)->get();
        $totalDeactivatedUsers = $this->user->where('role_id', 4)->where('is_active', 0)->where('user_referral_id', $user->id)->get();
        $affiliateUsers = $this->user->where('role_id', 5)->where('country_id', $user->country_id)->get();
        $newUsers = $this->user->where('role_id', 4)->where('user_referral_id', $user->id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user_referral_id', $user->id)->get();


        //Earnings
        $totalEarningsThisYear = $this->userSubscriptionHistory->whereIn('type', ['invoice'])
                                ->whereYear('created_at',Carbon::now()->year)->get();
        $totalEarningsOfYearAmount = 0;
        foreach ($totalEarningsThisYear as $key => $value) {
            $amount = json_decode($value->response_data)->amount_paid ?? 0;
            $totalEarningsOfYearAmount += number_format($amount / 100, 2);
        }

        $totalEarningsThisMonth = $this->userSubscriptionHistory->whereIn('type', ['invoice'])
                                ->select('users_subscriptions_history.*')
                                ->leftJoin('users', 'users_subscriptions_history.user_id', 'users.id')
                                ->whereMonth('users_subscriptions_history.created_at', Carbon::now()->month)
                                ->where('users.user_referral_id', $user->id)
                                ->get();
        $totalEarningsOfMonthAmount = 0;
        foreach ($totalEarningsThisMonth as $key => $value) {
            $amount = json_decode($value->response_data)->amount_paid ?? 0;
            $totalEarningsOfMonthAmount += number_format($amount / 100, 2);
        }

        return view('partner.index', compact('user', 'totalUsers', 'totalEarningsOfMonthAmount', 'totalEarningsThisYear','totalDeactivatedUsers','affiliateUsers','newUsers'));
    }


    //Super-admin profile
    public function profileEdit(){
        $user = app('truFlix')->getSessionUser();
        return view('zq.profile.index', compact('user'));
    }

    public function profileUpdate(User $zq, ProfileUpdateRequest $request){
        try {
            $response = DB::transaction(function () use ($request, $zq) {
                if($zq->email !== $request->email){
                    $checkEmail = $this->user->where('email', $request->email)->where('id', '!=', $zq->id)->get()->count();
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
                if ($zq->update($data)) {
                    return $this->success('Profile details updated successfully.', $zq);
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
}
