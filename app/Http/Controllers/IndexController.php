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
use App\Repositories\EntertainmentRepository;
use Illuminate\Support\Carbon;
use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\CountriesRepository;

class IndexController extends Controller
{
    use TruFlix, HttpResponses;

    protected $user;
    protected $userRepository;
    protected $userSubscriptions;
    protected $userSubscriptionHistory;
    protected $countryRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        UserSubscriptionsRepository $_userSubscriptions,
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory,
        CountriesRepository $countryRepository,
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
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
        if ($user) {
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
    public function zqDashboard()
    {
        $user = app('truFlix')->getSessionUser();
        return view('zq.index', compact('user'));
    }

    //Admin Dashboard
    public function adminDashboard()
    {

        $user = app('truFlix')->getSessionUser();
        $users = $this->user->where('role_id', 4)->get()->count();

        return view('admin.index', compact('users', 'user'));
    }

    //Partner Dashboard
    public function partnerDashboard()
    {
        $user = app('truFlix')->getSessionUser();

        return view('partner.index', compact('user'));
    }


    //Super-admin profile
    public function profileEdit()
    {
        $user = app('truFlix')->getSessionUser();
        return view('zq.profile.index', compact('user'));
    }

    public function profileUpdate(User $zq, ProfileUpdateRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request, $zq) {
                if ($zq->email !== $request->email) {
                    $checkEmail = $this->user->where('email', $request->email)->where('id', '!=', $zq->id)->get()->count();
                    if ($checkEmail) {
                        return $this->validation('Email is already exists.');
                    }
                }

                $data = $request->all();
                $newData = [];
                if (!empty($data['password']) && isset($data['password'])) {
                    $newData['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                $data = array_merge($data, $newData);
                if ($zq->update($data)) {
                    return $this->success('Profile details updated successfully.', $zq);
                }

                return $this->validation('Error while updating profile details. Please try again later.');
            });

            $response = $response->getData();
            if ($response->status) {
                return redirect()->back()->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
