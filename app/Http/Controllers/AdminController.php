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

class AdminController extends Controller
{

    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;

    public function __construct(
        User $_user,
        UserRepository $userRepository
    ) {
        $this->user = $_user;
        $this->userRepository = $userRepository;
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
        return view('zq.admin.list', compact('users', 'startDate', 'newRecords', 'deactivatedRecords'));
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
            if ($response->status) {
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
                if ($admin->email !== $request->email) {
                    $checkEmail = $this->userRepository->where('email', $request->email)->where('id', '!=', $admin->id)->get()->count();
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
                if ($admin->update($data)) {
                    return $this->success('Admin details updated successfully.', $admin);
                }

                return $this->validation('Error while updating admin details. Please try again later.');
            });

            $response = $response->getData();
            if ($response->status) {
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
            if ($response->status) {
                return redirect()->route('zq.admins.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function profileEdit()
    {
        $user = app('truFlix')->getSessionUser();
        return view('admin.profile.index', compact('user'));
    }

    public function profileUpdate(User $admin, ProfileAdminUpdateRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request, $admin) {
                if ($admin->email !== $request->email) {
                    $checkEmail = $this->user->where('email', $request->email)->where('id', '!=', $admin->id)->get()->count();
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
                if ($admin->update($data)) {
                    return $this->success('Profile details updated successfully.', $admin);
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

    public function adminFetchDataListForAjax(Request $request)
    {
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
        if (!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])) { //.... conduction's can add in this if clause

            if (!empty($data['search'])) {
                $users->where('role_id', 'Like', '%' . $data['search'] . '%')
                    ->orWhere('email', 'Like', '%' . $data['search'] . '%')
                    ->orWhere('name', 'Like', '%' . $data['search'] . '%');
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
        foreach ($users as $key => $user) {
            $activateButton = $user->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $user->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="edit">
                                <a href="' . route('zq.admins.edit', ['admin' => $user->id]) . '"> <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="' . route('zq.admins.soft-delete', ['admin' => $user->id]) . '" onclick=" return confirm(\'Are you sure to delete this admin?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="' . $user->id . '" data-action="' . $activateAction . '">
                                    ' . $activateButton . '
                                </a>
                            </li>
                        </ul>';


            $newUser = [
                'sno'       => $key + 1,
                'email'     => $user->email,
                'name'      => "<a href='" . route('zq.admins.overview.details', ['admin' => $user->id]) . "'>" . $user->name . "</a>",
                //'username'  => $user->username,
                'movies'    =>  0,
                'shows'     =>  0,
                'events'    =>  0,
                'date_added' => date('M d, Y', strtotime($user->created_at)),
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
}
