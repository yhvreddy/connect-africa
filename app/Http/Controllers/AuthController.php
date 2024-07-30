<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\TruFlix;
class AuthController extends Controller
{

    use TruFlix;

    protected $user;
    protected $role;
    
    public function __construct(User $_user, Role $_role) {
        $this->user = $_user;
        $this->role = $_role;
    }

    public function index() {

        $user = $this->getSessionUser();
        if($user){
            return redirect($this->redirectToDashboard($user));
        }

        return view('auth.login');
    }

    public function loginAccess(LoginUserRequest $request) {
        try {
            $role = $this->role->where('slug', $request->role)->whereNotIn('slug', ['user', 'affiliate'])->first();
            $user = $this->user->where('email', $request->username)->orWhere('username', $request->username)->first();
            if(!$user){
                return redirect()->back()->with('failed', 'No Credentials Found with this username or email.');
            }

            if(!$role){
                return redirect()->back()->with('failed', 'Invalid login or No access to login');
            }
            // $guard = $this->getRole($user);
            // if(!$guard){
            //     $guard = 'web';
            // }

            if($role){
                $guard = $role->slug;
            }else{
                $guard = 'zq';
            }

            $username = $request->username;
            if(Auth::guard($guard)->attempt(['email' => $username, 'password' => $request->password, 'role_id' => $role->id, 'is_active' => 1])){
                // Authentication passed...
                $user = Auth::guard($guard)->user();

                if(empty($user->email_verified_at)){
                    Auth::guard($guard)->logout();
                    return redirect()->back()->with('failed', 'Please verify your registered email.');
                }



                $redirect = $this->redirectToDashboard($user);
                Log::info("Login: ".$user->name.' as successfully logged in. && Redirected To : '.$redirect);
                return redirect($redirect)->with('success', $user->name.' has been successfully logged in. welcome back..!');
            }

            //Failed to authenticate user credentials...
            Log::debug("Failed: Invalid Credentials, Please check Email and Password.");
            return redirect()->back()->with('failed', 'Invalid Credentials, Please check Email and Password.');

        } catch (\Throwable $th) {
            Log::error("Cache: ".$th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    private function redirectToDashboard($user){
        if($user->isZq()){
            //Super-admin redirect url...
            return route('zq.dashboard');
        }elseif($user->isAdmin()){
            //Admin redirect url...            
            return route('admin.dashboard');
        }elseif($user->isPartner()){
            //Partner redirect url...
            return route('partner.dashboard');
        }

        return null;
    }

    private function getRole($user){
        $request = request();

        if($user){
            $role = $user->role->slug;
        }

        $role = $request->role ?? ($role ?? null);
        return $role;
    }


    public function logOutSession(){
        $guard = app('truFlix')->getCurrentGuard();
        if(!$guard){
            Session::flush();
            Auth::logout();
            return redirect()->route('login');
        }
        
        Session::flush();
        Auth::guard($guard)->logout();
        return redirect()->route('login', ['role' => $guard]);
    }
}
