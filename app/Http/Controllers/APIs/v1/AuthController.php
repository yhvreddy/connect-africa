<?php

namespace App\Http\Controllers\APIs\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\APIs\AuthLoginRequest;
use App\Http\Requests\APIs\UserUpdateRegisterRequest;
use App\Http\Requests\APIs\UserUpdatePinRequest;
use App\Http\Requests\ForgetPinRequest;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\v1\UserResource;
use App\Http\Traits\TruFlix;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\VerifyUserTokenRequest;
use App\Repositories\CountriesRepository;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class AuthController extends Controller
{
    use HttpResponses, TruFlix;

    protected $user;
    protected $userRepository;
    protected $country;

    public function __construct(
        User $_user,
        UserRepository $userRepository,
        CountriesRepository $country
    ) {
        $this->user             =   $_user;
        $this->userRepository   =   $userRepository;
        $this->country          =   $country;
    }

    //Auth User
    public function login(AuthLoginRequest $request){
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            if(!Auth::attempt(['mobile' => $request->mobile, 'password' => $request->pin, 'role_id' => 4])) {
                return $this->validation('Invalid credentials.');
            }

            $user = $request->user();
            $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
            $user->accessToken = $tokenResult->plainTextToken;
            $user->token_type = 'Bearer';
            $user->send_password = false;
            $user = new UserResource($user);
            return $this->success('Login Success', $user);

        } catch (\Throwable $th) {
            return $this->internalServer('Something wrong', $th->getMessage());
        }
    }

    public function registerNewUser(UserRegisterRequest $request){
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            if(!isset($request->mobile) && !is_numeric($request->mobile)){
                return $this->validation('Mobile Number Required.');
            }

            if((isset($request->mobile) && is_numeric($request->mobile)) && $request->step == 'mobile'){
                $users = $this->user->where('mobile', $request->mobile)->get();
                if($users->count()){
                    return $this->validation('Mobile Number Already Exists.');
                }

                $otp = 123456; //rand(111111, 999999);
                $user = $this->user->create([
                    'username'  =>  $request->mobile,
                    'mobile'    =>  $request->mobile,
                    'otp'       =>  $otp,
                    'role_id'   =>  4
                ]);

                //Todo:: Store OTP In DB and Send OTP Message
                $data = ['otp' => $user->otp];
                return $this->success('OTP Sent', $data);
            }

            $user = $this->user->where('mobile', $request->mobile)->first();
            if (!$user) {
                return $this->validation('No Account Found.');
            }

            if ((isset($request->otp) && is_numeric($request->otp)) && $request->step == 'otp') {
                //TODO:: Verify OTP with Request OTP
                if($user->otp != $request->otp){
                    return $this->validation('Invalid OTP.');
                }

                $user->email_verified_at = now();
                $user->save();

                return $this->success('OTP Verified.', new UserResource($user));
            }

            if ((isset($request->pin) && is_numeric($request->pin)) && $request->step == 'pin') {
                if(!isset($request->pin) && !is_numeric($request->pin)){
                    return $this->validation('PIN Required.');
                }

                $user->password     =   Hash::make($request->pin);
                $user->access_code  =   $request->pin;
                $user->save();

                if(Auth::loginUsingId($user->id)){
                    $user = $request->user();
                    $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
                    $user->accessToken = $tokenResult->plainTextToken;
                    $user->token_type = 'Bearer';
                    $user = new UserResource($user);
                }

                return $this->objectCreated('User created successfully.', $user);
            }

            return $this->validation('Sorry, Invalid Request To Create Account.');

        } catch (\Throwable $th) {
            return $this->internalServer('Something wrong', $th->getMessage());
        }
    }

    public function forgetPin(ForgetPinRequest $request){
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            if(!isset($request->mobile) && !is_numeric($request->mobile)){
                return $this->validation('Mobile Number Required.');
            }

            $user = $this->user->where('mobile', $request->mobile)->first();
            if (!$user) {
                return $this->validation('No Account Found.');
            }

            if($user && (isset($request->mobile) && is_numeric($request->mobile)) && $request->step == 'mobile'){

                $otp = 123456; //rand(111111, 999999);
                $user->otp =  $otp;
                $user->save();

                //Todo:: Store OTP In DB and Send OTP Message
                $data = ['otp' => $user->otp];
                return $this->success('OTP Sent', $data);
            }



            if ($user && (isset($request->otp) && is_numeric($request->otp)) && $request->step == 'otp') {
                //TODO:: Verify OTP with Request OTP
                if($user->otp != $request->otp){
                    return $this->validation('Invalid OTP.');
                }

                $user->email_verified_at = now();
                $user->save();

                return $this->success('OTP Verified.', new UserResource($user));
            }

            if ($user && (isset($request->pin) && is_numeric($request->pin)) && $request->step == 'pin') {
                if(!isset($request->pin) && !is_numeric($request->pin)){
                    return $this->validation('PIN Required.');
                }

                $user->password     =   Hash::make($request->pin);
                $user->access_code  =   $request->pin;
                $user->save();

                if(Auth::loginUsingId($user->id)){
                    $user = $request->user();
                    $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
                    $user->accessToken = $tokenResult->plainTextToken;
                    $user->token_type = 'Bearer';
                    $user = new UserResource($user);
                }

                return $this->objectCreated('User Pin Updated successfully.', $user);
            }

            return $this->validation('Sorry, Invalid Request To Update PIn.');

        } catch (\Throwable $th) {
            return $this->internalServer('Something wrong', $th->getMessage());
        }
    }

    public function getUserDetails(Request $request){
        $user = $request->user();
        if($user){
            return $this->success('User Details', new UserResource($user));
        }

        return $this->unauthorized('Invalid User Request or Unauthorized Access.');
    }

    public function updateUserPin(UserUpdatePinRequest $request){
        if(!$request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $request->user();
        if(!$user){
            return $this->validation('Unauthenticated User Access.');
        }

        try {
            $response = DB::transaction(function () use ($request, $user) {

                $data = $request->all();

                if($request->password !== $request->confirm_password){
                    return $this->validation('Sorry, Password and confirm password not matching.');
                }

                $data['password'] = Hash::make($data['password']);
                unset($data['confirm_password']);

                if ($user->update($data)) {
                    //Todo::Send mail to user with activation link
                    return $this->objectCreated('Password Updated Successfully.', $user);
                }

                return $this->validation('Sorry, Failed To Update Password.');
            });

            return $response;

        } catch (\Throwable $th) {
            return $this->validation($th->getMessage());
        }
    }

    public function updateUserDetails(UserUpdateRequest $request)
    {
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            $user = $request->user();
            if (!$user) {
                return $this->validation('Invalid Request To Update Details.');
            }

            if(isset($request->password) && !is_numeric($request->password)){
                return $this->validation('Invalid Pin Number Format.');
            }

            $checkMobile = $this->user->where('mobile', $request->mobile)->where('id', '!=', $user->id)->get();
            if($checkMobile->count()){
                return $this->validation('Sorry, Mobile Number Already Exists.');
            }

            if($user){

                $country = null;
                if(isset($request->country) && !empty($request->country)){
                    $country = $this->country->where('name', $request->country)->first();
                    if(!$country){
                        $country = $this->country->create([
                            'name' => $request->country,
                        ]);
                    }
                }

                if($country){
                    $user->country_id = $country->id;
                }

                $user->name = $request->name;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                if (isset($request->password)) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();
                return $this->success('User Updated successfully.', new UserResource($user));
            }

            return $this->validation('Sorry, Failed To Update Details.');

        } catch (\Throwable $th) {
            return $this->internalServer('Something wrong', $th->getMessage());
        }
    }

    public function verifyUserToken(VerifyUserTokenRequest $request){
        try{
            $authorizationHeader = $request->token;
            if (!$authorizationHeader) {
                return $this->validation('Authorization token not found');
            }

            $token = str_replace('Bearer ', '', $authorizationHeader);
            $tokenDetails = PersonalAccessToken::findToken($token);
            if (!$tokenDetails) {
                return $this->validation('Token not found');
            }

            $user = $tokenDetails->tokenable;
            if (!$user || !$user instanceof \App\Models\User) {
                return $this->validation('Token does not belong to a valid user');
            }

            if (Carbon::now()->greaterThan($tokenDetails->expires_at)) {
                return $this->validation('Token has expired');
            }

            $check = Auth::loginUsingId($user->id);
            if ($check) {
                $user = auth()->user();
                // $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
                $user->accessToken = $token; //$tokenResult->plainTextToken;
                $user->token_type = 'Bearer';
                $user->send_password = false;
                $user = new UserResource($user);
                return $this->success('Token verified successfully.', $user);
            }

            return $this->validation('Wrong token or token expired.');

        }catch(\Exception $e){
            return $this->internalServer($e->getMessage());
        }
    }

    public function logOutUser(Request $request){
        if($request->user()){
            $request->user()->tokens()->delete();
            return $this->success('User logged out successfully.');
        }

        return $this->validation('Invalid request to User logout.');
    }
}
