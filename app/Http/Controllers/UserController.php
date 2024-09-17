<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterStoreRequest;
use App\Http\Requests\UserRegisterUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use App\Repositories\CountriesRepository;
use Illuminate\Support\Carbon;


class UserController extends Controller
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
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $users = $this->userRepository->where('role_id', 4)->get();

        $newRecords = $this->userRepository
            ->where('is_active', 1)
            ->where('role_id', 4)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();

        $deactivatedRecords = $this->userRepository
            ->where('role_id', 4)
            ->where('is_active', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $countries = $this->countryRepository->orderBy('name', 'asc')->get();
        return view('admin.user.list', compact('users', 'startDate', 'newRecords', 'deactivatedRecords', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRegisterStoreRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();
                $newData = [];
                if (!empty($data['password']) && isset($data['password'])) {
                    $newData['password'] = Hash::make($data['password']);
                } else {
                    $newData['password'] = Hash::make('admin@123!');
                }
                $newData['role_id'] = 4;


                $data = array_merge($data, $newData);
                $user = $this->userRepository->create($data);
                if ($user->save()) {
                    return $this->success('User details saved successfully.', $user);
                }

                return $this->validation('Error while saving user details. Please try again later.');
            });

            $response = $response->getData();
            if ($response->status) {
                return redirect()->route('admin.users.index')->with('success', $response->message);
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
    public function edit(User $user)
    {
        return view('zq.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRegisterUpdateRequest $request, User $user)
    {
        try {
            $response = DB::transaction(function () use ($request, $user) {
                if ($user->email !== $request->email) {
                    $checkEmail = $this->userRepository->where('email', $request->email)->where('id', '!=', $user->id)->get()->count();
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
                if ($user->update($data)) {
                    return $this->success('User details updated successfully.', $user);
                }

                return $this->validation('Error while updating user details. Please try again later.');
            });

            $response = $response->getData();
            if ($response->status) {
                return redirect()->route('admin.users.index')->with('success', $response->message);
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
    public function softDelete(User $user)
    {
        try {
            $response = DB::transaction(function () use ($user) {
                if (isset($user->subscription)) {
                    $user->subscription->delete();
                }

                if ($user->forceDelete()) {
                    return $this->success('User deleted successfully.');
                }

                return $this->validation('Invalid Request to delete user.');
            });

            $response = $response->getData();
            if ($response->status) {
                return redirect()->route('admin.users.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * User Details Page
     */
    public function userDetails(Request $request, User $user)
    {
        $referredUsers = $user->referredUsers;
        return view('default.users.view', compact('user', 'referredUsers'));
    }

    public function fetchDataListForAjax(Request $request)
    {
        // Defined Columns
        $columnArray = [
            'id',
            'name',
            'email',
            'created_at',
            // 'stripe_customer_id',
            'status',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $users = User::where('role_id', 4); // Initialize $users here

        $totalData = $users->count();
        $totalFiltered = $totalData;

        // Filter Data
        if (!empty($data['search']) || !empty($data['duration']) || !empty($data['status']) || !empty($data['country_id'])) {

            if (!empty($data['country_id'])) {
                $users->where('country_id', $data['country_id']);
            }

            if (!empty($data['search'])) {
                $users->where(function ($query) use ($data) {
                    $query->where('role_id', 'like', '%' . $data['search'] . '%')
                        ->orWhere('email', 'like', '%' . $data['search'] . '%')
                        ->orWhere('name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('username', 'like', '%' . $data['search'] . '%');
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

            $totalFiltered = $users->count();
        }

        $users = $users
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        foreach ($users as $key => $user) {
            $user->sno  = $key + 1;
            $user->actions = '<ul class="action align-center">
                                <li class="edit">
                                    <a href="' . route('admin.users.edit', ['user' => $user->id]) . '" data-toggle="tooltip" data-placement="top" title="Edit">
                                     <i class="icon-pencil-alt"></i>
                                    </a>
                                </li>

                                <li class="delete">
                                    <a href="' . route('admin.users.soft-delete', ['user' => $user->id]) . '" onclick=" return confirm(\'Are you sure to delete user?\');" data-toggle="tooltip" data-placement="top" title="Delete">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            </ul>';



            // <li class="activate">
            //     <a href="javascript:void(0);" class="btn-activate" data-id="'.$user->id.'" data-action="'.($user->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($user->is_active ? 'Deactivate' : 'Activate').' User" >
            //         '.($user->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
            //     </a>
            // </li>

            $user->is_active   = $user->is_active === 1 ? 'Active' : 'Deactivate';
            $user->created_date = date('M d, Y', strtotime($user->created_at));
            $user->name  = $user?->name ?? '';
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = $users;
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $userId, $action)
    {
        $user = User::findOrFail($userId);
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
