<?php

namespace App\Http\Controllers\APIs\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\VerifyReferralCodeRequest;
use App\Http\Requests\VerifyOTPCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserRepository;
use App\Http\Resources\v1\UserResource;
use App\Http\Resources\v1\UserForSupResource;
use App\Mail\UserRequestOTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
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
        //
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
    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Auth"},
     *     summary="Create User Account",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Login password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function store(UserRegisterRequest $request)
    {
        if(!$request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        // $mailData = [
        //     'subject' => 'Truflix account verification otp code.',
        //     'data' => [
        //         'otp' => 123456,
        //         'name' => $request->name,
        //         'email' => $request->email,
        //     ]
        // ];
        // Mail::to('yhvreddydev@gmail.com')->send(new UserRequestOTPMail($mailData));


        // dd("==");

        if(!isset($request->referral_code) &&  empty($request->referral_code)){
            Log::info('1 Referral code is missing. " '.json_encode($request->all()));
            return $this->validation('Invalid request to register account.');
        }


        $referralUser = $this->user->whereIn('role_id', [2, 3, 4, 5])
                ->where('access_code', $request->referral_code)->where('is_active', 1)->first();
        if(!$referralUser){
            Log::info('2 Invalid referral code or No code found in database : '.$request->referral_code.'" '.json_encode($request->all()));
            return $this->validation('Invalid request to register account.');
        }else{
            if($request->referral_code !== $referralUser->access_code){
                Log::info('3 Referral code is not matching with '.$referralUser->access_code.' = '.$request->referral_code.' " '.json_encode($request->all()));
                return $this->validation('Invalid request to register account.');
            }
        }

        try {
            $response = DB::transaction(function () use ($request, $referralUser) {

                $data = $request->all();
                $data['password'] = Hash::make($data['password'] ?? 'admin@123!');
                $data['username'] = $data['email'];
                $data['role_id'] = 4;
                $data['otp'] = rand(1000, 9999);
                $data['access_code'] = $this->generateRandomCode(6);
                $data['user_referral_id'] = $referralUser->id;
                $data['country_id'] = $referralUser->country->id;
                $data['location_availability'] = $request->location_availability;
                $data['referring_members'] = $request->referring_members;

                $user = $this->userRepository->create($data);
                if ($user) {

                    if(Auth::loginUsingId($user->id)){
                        $user = $request->user();
                        $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
                        $user->accessToken = $tokenResult->plainTextToken;
                        $user->token_type = 'Bearer';
                        $user = new UserResource($user);
                    }

                    //Create User On Supportania
                    //$this->sendUserDetailsRequest($user);


                    //Create User On Supportania
                    $supportaniaCreateUserURL = config('app.supportania_url').'/api/v1/user-create';
                    $user->password = $user->password;
                    $payload = [
                        'name'                  =>  $user->name,
                        'email'                 =>  $user->email,
                        'password'              =>  $user->password ?? null,
                        'mobile'                =>  $user->mobile ?? null,
                        'access_code'           =>  $user->access_code ?? null,
                        'country'               =>  $user->country->name ?? null,
                        'country_id'            =>  $user->country->id ?? null,
                        'user_referral_id'      =>  $user->user_referral_id,
                        'location_availability' =>  $user->location_availability,
                        'referring_members'     =>  $user->referring_members,
                    ];
                    Log::debug("Request Sending Payload to Supportania : ". json_encode($payload));


                    //Todo::Send mail to user with activation link
                    $mailData = [
                        'subject' => 'Truflix account verification otp code.',
                        'data' => [
                            'otp' => $data['otp'],
                            'name' => $user->name,
                            'email' => $user->email,
                        ]
                    ];
                    // Mail::to($user->email)->send(new UserRequestOTPMail($mailData));
                    return $this->objectCreated('User created successfully.', $user);
                }

                return $this->validation('Sorry, Failed to create account.');
            });

            return $response;

        } catch (\Throwable $th) {
            return $this->validation($th->getMessage());
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/verify-access-code",
     *     tags={"Auth"},
     *     summary="Verify Referral Code",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="referral_code",
     *         in="query",
     *         description="Referral Code",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function verifyAccessCode(VerifyReferralCodeRequest $request){
        if(!$request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $this->user->whereIn('role_id', [3, 4, 5])
                ->where('access_code', $request->referral_code)->where('is_active', 1)->first();
        if(!$user){
            return $this->validation('Invalid Referral Code');
        }

        if($user->access_code === $request->referral_code){

            $data = [
                'id'    =>  $user->id,
                'name'  =>  $user->name,
                'referral'  =>  [
                    'country_id'=>  $user->country->id,
                    'code'      =>  $user->access_code,
                    'country'   =>  $user->country->name,
                    'limit'     =>  $user->country->code
                ]
            ];

            return $this->success('Referral code is verified.', $data);
        }

        return $this->validation('Failed to verify referral code.');
    }

    public function verifyOTPCode(VerifyOTPCodeRequest $request){
        // Validate the otp code and check whether it's valid or not.
        if(!$request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $request->user();
        if(!$user){
            return $this->validation('Unauthenticated.');
        }

        if(!empty($user->email_verified_at)){
            return $this->validation('Account is already verified.');
        }

        //otp env('APP_ENV') == 'local' ? 0000 : $user->otp;
        $definedOTP = 0000;
        if($definedOTP != $request->otp){
            return $this->validation('Invalid OTP Code');
        }

        $user->email_verified_at = now();
        if($user->save()){
            return $this->success('Account is successfully verified.', new UserResource($user));
        }

        return $this->validation('Failed to verify account. Please try again later.');
    }

    // Fetch Users to export
    public function fetchUsersForExport(){
        $users = $this->user->select(
                'users.*',
            )->leftJoin('users_subscriptions', 'users_subscriptions.user_id', 'users.id')->where('users.role_id', 4)
            ->where('users_subscriptions.subscription_status', 'active')
            ->get();
        $newUserData = [];
        foreach($users as $key => $user){
            $user->send_password = true;
            $newUserData[] = new UserForSupResource($user);
        }

        return response()->json($newUserData, 200);
    }
}
