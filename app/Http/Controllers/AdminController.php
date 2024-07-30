<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\AdminRegisterUpdateRequest;
use App\Http\Requests\ProfileAdminUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use App\Repositories\EntertainmentRepository;
use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\CategoriesRepository;
class AdminController extends Controller
{

    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;
    protected $entertainment;
    protected $categoryRepository;
    protected $entertainmentRepository;
    protected $entertainmentAdditionalRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        EntertainmentRepository $_entertainment,
        CategoriesRepository $_categoryRepository,
        EntertainmentRepository $_entertainmentRepository,
        EntertainmentAdditionalRepository $_entertainmentAdditionalRepository
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
        $this->entertainment = $_entertainment;
        $this->categoryRepository = $_categoryRepository;
        $this->entertainmentRepository = $_entertainmentRepository;
        $this->entertainmentAdditionalRepository = $_entertainmentAdditionalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $users = $this->userRepository->where('role_id', 2)->get();
        $newRecords = $this->userRepository->where('role_id', 2)
        ->where('created_at', '>=', $startDate)->get();
        $deactivatedRecords = $this->userRepository
        ->where('role_id', 2)
        ->where('is_active', 0)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();
        return view('zq.admin.list', compact('users','startDate','newRecords','deactivatedRecords'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('zq.admin.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRegisterRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();
                $data['password'] = Hash::make($data['password'] ?? 'admin@123!');
                $data['username'] = $data['email'];
                $data['role_id'] = 2;
                $data['email_verified_at'] = now();
                $data['access_code'] = $this->generateRandomCode(6);
                $saveAdmin = $this->userRepository->create($data);
                if ($saveAdmin) {
                    return $this->objectCreated('Admin added successfully.', $saveAdmin);
                }

                return $this->validation('Failed to add admin details.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.admins.index')->with('success', $response->message);
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
    public function edit(User $admin)
    {
        return view('zq.admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRegisterUpdateRequest $request, User $admin)
    {
        try {
            $response = DB::transaction(function () use ($request, $admin) {
                if($admin->email !== $request->email){
                    $checkEmail = $this->userRepository->where('email', $request->email)->where('id', '!=', $admin->id)->get()->count();
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
                if ($admin->update($data)) {
                    return $this->success('Admin details updated successfully.', $admin);
                }

                return $this->validation('Error while updating admin details. Please try again later.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.admins.index')->with('success', $response->message);
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
    public function softDelete(User $admin)
    {
        try {
            $response = DB::transaction(function () use ($admin) {
                if ($admin->delete()) {
                    return $this->success('Admin deleted successfully.');
                }

                return $this->validation('Invalid Request to delete admin.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.admins.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function profileEdit(){
        $user = app('truFlix')->getSessionUser();
        return view('admin.profile.index', compact('user'));
    }

    public function profileUpdate(User $admin, ProfileAdminUpdateRequest $request){
        try {
            $response = DB::transaction(function () use ($request, $admin) {
                if($admin->email !== $request->email){
                    $checkEmail = $this->user->where('email', $request->email)->where('id', '!=', $admin->id)->get()->count();
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
                if ($admin->update($data)) {
                    return $this->success('Profile details updated successfully.', $admin);
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

    public function adminFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'name',
            'email',
            'created_at',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $users = $this->userRepository->where('role_id', 2);
        

        $totalData = count($users->get());
        $totalFiltered = $totalData;

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $users->where('role_id', 'Like', '%'.$data['search'].'%')
                           ->orWhere('email', 'Like', '%'.$data['search'].'%')
                           ->orWhere('name', 'Like', '%'.$data['search'].'%');
            }

        // Filter by user status (active or deactivated)
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 'active':
                    $users->where('is_active', 1);
                    break;
                case 'deactivated':
                    $users->where('is_active', 0);
                    break;
            }
        }


            $totalFiltered = count($users->get());
        }

        $users = $users
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

        //Customize or add additional data in below loop
        $newUsers = [];
        foreach($users as $key => $user){
            $activateButton = $user->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $user->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="edit">
                                <a href="'.route('zq.admins.edit', ['admin' => $user->id]).'"> <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('zq.admins.soft-delete', ['admin' => $user->id]).'" onclick=" return confirm(\'Are you sure to delete this admin?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$user->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';

            $movies = $this->entertainment->where('category_id', 1)->where('user_id', $user->id)->where('is_active', 1)->get();
            $shows = $this->entertainment->where('category_id', 2)->where('user_id', $user->id)->where('is_active', 1)->get();
            $events = $this->entertainment->where('category_id', 3)->where('user_id', $user->id)->where('is_active', 1)->get();

            $newUser = [
                'sno'       => $key +1,
                'email'     => $user->email,
                'name'      => "<a href='".route('zq.admins.overview.details', ['admin' => $user->id])."'>".$user->name."</a>",
                //'username'  => $user->username,
                'movies'    =>  $movies->count(),
                'shows'     =>  $shows->count(),
                'events'    =>  $events->count(),
                'date_added'=> date('M d, Y', strtotime($user->created_at)),
                'actions'   => $actions
            ];
            array_push($newUsers, $newUser);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newUsers);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $adminId, $action)
    {
        $admin = User::findOrFail($adminId);

        if (!$admin) {
            return response()->json(['error' => 'admin not found.'], 404);
        }

        if ($action === 'activate') {
            $admin->is_active = 1;
        } elseif ($action === 'deactivate') {
            $admin->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $admin->save();

        return response()->json(['message' => 'Admin status updated successfully.'], 200);
    }


    public function adminOverview(User $admin){
        //Categories
        $categoryMovies = $this->categoryRepository->where('slug', 'movies')->first();
        $categoryShows = $this->categoryRepository->where('slug', 'shows')->first();
        $categoryEvents = $this->categoryRepository->where('slug', 'events')->first();

        //Total
        $allCount = $this->entertainmentRepository->count();
        $adminAllCount = $this->entertainmentRepository->where('user_id', $admin->id)->count();

        $moviesCount = $this->entertainmentRepository
                        ->where('category_id', $categoryMovies->id)
                        ->count();

        $adminMoviesCount = $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->where('user_id', $admin->id)
                            ->count();

        $showsCount = $this->entertainmentRepository->where('category_id', $categoryShows->id)->count();
        $adminShowsCount = $this->entertainmentRepository
                           ->where('category_id', $categoryShows->id)
                           ->where('user_id', $admin->id)
                           ->count();

        $eventsCount = $this->entertainmentRepository->where('category_id', $categoryEvents->id)->count();
        $adminEventsCount = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->where('user_id', $admin->id)
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
                            ->where('user_id', $admin->id)
                            ->count();

        $showsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryShows->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();
        $adminShowsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryShows->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('user_id', $admin->id)
                            ->count();

        $eventsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count();
        $adminEventsCountMonth = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('user_id', $admin->id)
                            ->count();

        $totalCountMonth = $moviesCountMonth + $showsCountMonth + $eventsCountMonth;
        $adminTotalCountMonth = $adminMoviesCountMonth + $adminShowsCountMonth + $adminEventsCountMonth;

        //week overview
        $moviesThisWeek =   $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                            ->count();
        $adminMoviesThisWeek =   $this->entertainmentRepository
                            ->where('category_id', $categoryMovies->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                            ->where('user_id', $admin->id)
                            ->count();

        $showsThisWeek = $this->entertainmentRepository
                        ->where('category_id', $categoryShows->id)
                        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->count();
        $adminShowsThisWeek = $this->entertainmentRepository
                        ->where('category_id', $categoryShows->id)
                        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->where('user_id', $admin->id)
                        ->count();

        $eventsThisWeek = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                            ->count();
        $adminEventsThisWeek = $this->entertainmentRepository
                            ->where('category_id', $categoryEvents->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                            ->where('user_id', $admin->id)
                            ->count();

        $totalCountWeek = $moviesThisWeek + $showsThisWeek + $eventsThisWeek;
        $adminTotalCountWeek = $adminMoviesThisWeek + $adminShowsThisWeek + $adminEventsThisWeek;


        //Today Overview
        $moviesToday = $this->entertainmentRepository
                        ->where('category_id', $categoryMovies->id)
                        ->whereDate('created_at', Carbon::today())
                        ->count();
        $adminMoviesToday = $this->entertainmentRepository
                        ->where('category_id', $categoryMovies->id)
                        ->whereDate('created_at', Carbon::today())
                        ->where('user_id', $admin->id)
                        ->count();

        $showsToday = $this->entertainmentRepository
                        ->where('category_id', $categoryShows->id)
                        ->whereDate('created_at', Carbon::today())
                        ->count();
        $adminShowsToday = $this->entertainmentRepository
                        ->where('category_id', $categoryShows->id)
                        ->whereDate('created_at', Carbon::today())
                        ->where('user_id', $admin->id)
                        ->count();

        $eventsToday = $this->entertainmentRepository
                        ->where('category_id', $categoryEvents->id)
                        ->whereDate('created_at', Carbon::today())
                        ->count();
        $adminEventsToday = $this->entertainmentRepository
                        ->where('category_id', $categoryEvents->id)
                        ->whereDate('created_at', Carbon::today())
                        ->where('user_id', $admin->id)
                        ->count();

        $totalCountToday = $moviesToday + $showsToday + $eventsToday;
        $adminTotalCountToday = $adminMoviesToday + $adminShowsToday + $adminEventsToday;

        $isRequiredTruflix = false;

        return view('zq.admin.over_view', compact('admin','allCount','moviesCount','showsCount','eventsCount','moviesCountMonth','showsCountMonth','eventsCountMonth','moviesThisWeek','showsThisWeek','eventsThisWeek','moviesToday','showsToday','eventsToday','totalCountMonth','totalCountWeek','totalCountToday', 'adminMoviesCount', 'adminShowsCount', 'adminEventsCount', 'adminAllCount', 'adminTotalCountMonth', 'adminEventsCountMonth', 'adminShowsCountMonth', 'adminMoviesCountMonth', 'adminTotalCountWeek', 'adminMoviesThisWeek', 'adminShowsThisWeek', 'adminEventsThisWeek', 'adminTotalCountToday', 'adminMoviesToday', 'adminShowsToday', 'adminEventsToday', 'isRequiredTruflix'));
    }
}
