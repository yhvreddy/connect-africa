<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

trait TruFlix
{

    public function getSessionUser()
    {
        $slug = request()->role ?? null;
        $user = null;
        if (Auth::guard('zq')->check()) {
            $user = Auth::guard('zq')->user();
        } elseif (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } elseif (Auth::guard('partner')->check()) {
            $user = Auth::guard('partner')->user();
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        }

        return $user;
    }

    //Generate slug by title and id.
    public function generateSlug($title, $additional = null)
    {
        // Generate a slug from the title
        $slug = Str::slug($title);

        // Optionally, you can append a unique identifier to the slug to ensure uniqueness
        // Example: $slug = Str::slug($title) . '-' . uniqid();

        return $slug;
    }

    public function uploadWithName($request, $fieldName, $directionFolderPath)
    {
        // Process the uploaded file
        $file = $request->file($fieldName);
        if (!$file) {
            return null;
        }

        $path = $file->store('public/' . $directionFolderPath);
        $name = $file->getClientOriginalName();
        $extension  = $file->getClientOriginalExtension();

        return [
            'path'      => $path,
            'name'      => $name,
            'extension' => $extension
        ];
    }

    public function uploadWithFile($file, $directionFolderPath)
    {
        // Process the uploaded file
        $path = $file->store('public/' . $directionFolderPath);
        $name = $file->getClientOriginalName();
        $extension  = $file->getClientOriginalExtension();

        return [
            'path'      => $path,
            'name'      => $name,
            'extension' => $extension
        ];
    }

    //Single File Upload
    public function uploadFile($request, $inputName, $folderPath)
    {
        $filepath = 'uploads/' . $folderPath . '/';
        if ($request->hasfile($inputName) && $_FILES[$inputName]['name'] != '') {
            $file = $request->file($inputName);
            $extension = $file->getClientOriginalExtension();
            $filename = time() . 'RD' . rand(1, 999) . '.' . $extension;
            $file->move($filepath, $filename);
            $uploadData = $filepath . $filename;
        } else {
            $uploadData = null;
        }
        return $uploadData;
    }

    //Multi File Upload
    public function multiUploadFiles($request, $inputName, $folderPath)
    {
        $images = [];
        $filePath = 'uploads/' . $folderPath . '/';
        if ($request->hasfile($inputName)) {
            $files = $request->file($inputName);
            //$allowedFileExtension=['pdf','jpg','png','docx'];
            foreach ($files as $k => $file) {
                //$filename  = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . 'RD' . rand(1, 999) . '.' . $extension;
                $file->move($filePath, $fileName);
                $images[$k] = $filePath . $fileName;
            }
        }
        return $images;
    }


    public function generateUniqueReferralCode($length = 6)
    {
        $code = Str::random($length);

        // Check if the code already exists
        while (User::where('access_code', $code)->exists()) {
            $code = Str::random($length);
        }

        return $code;
    }

    public function getFilePath($value, $fullPath = false)
    {
        $blank = 'assets/src/images/blank.png';
        $filePath = $fullPath ? asset($blank) : $blank;
        if (!empty($value) && \Illuminate\Support\Facades\Storage::exists($value)) {
            $filePath = $fullPath ? asset(\Illuminate\Support\Facades\Storage::url($value)) : \Illuminate\Support\Facades\Storage::url($value);
        }
        return $filePath;
    }


    public function extractedPaginateParams($model)
    {

        $request = Request::capture();

        // Extract pagination parameters
        $page = $model->currentPage();
        $perPage = $model->perPage();
        $total = $model->total();
        $lastPage = $model->lastPage();
        $path = $request->url(); // URL of the current page
        $queryString = $request->query(); // Query string parameters

        // Pagination details for passing to other parts of the application
        $paginationData = [
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'path' => $path,
            'queryString' => $queryString,
        ];
        return $paginationData;
    }

    public function convertDateTimeFormTimestamp($timestamp)
    {
        $date = Carbon::createFromTimestamp($timestamp);    // Create a Carbon instance from the timestamp
        $formatted_date = $date->format('Y-m-d H:i:s');     // Format the Carbon instance as per the desired format
        return $formatted_date;                             // Output the formatted date
    }


    // Function to generate random code
    public function generateRandomCode($length = 6)
    {
        $devLength = $length / 2;
        // Generate random alphabetic characters
        $alphas = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $devLength);

        // Generate random numeric characters
        $numbers = substr(str_shuffle("0123456789"), 0, $devLength);

        // Concatenate alphabetic and numeric characters
        $code = $alphas . $numbers;

        // Shuffle the combined string for randomness
        $code = str_shuffle($code);

        return strtoupper($code);
    }

    //Common Function
    public function curlPost($url, $data)
    {
        $requestData = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        Log::info("curlPost Start");
        Log::info(print_r($data, true));
        Log::info(print_r($response, true));
        Log::info("curlPost End");
        return $response;
    }

    public function sendUserDetailsRequest($user)
    {
        //Create User On Supportania
        $supportaniaCreateUserURL = config('app.supportania_url') . '/api/v1/user-create';
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
        Log::debug("Request Sending Payload to Supportania : " . json_encode($payload));
        $supportaniaCreateUser = $this->curlPost($supportaniaCreateUserURL, $payload);
        // $supportaniaCreateUser = Http::post($supportaniaCreateUserURL, $payload);
        Log::info("Request Response From Supportania : ");
        Log::debug(print_r($supportaniaCreateUser, true));
        return $supportaniaCreateUser;
    }

    public function updateSubscriptionData($request, $subscription)
    {
        $subscriptionPlan = $this->subscriptionPlans->find($request->subscription_plan_id);

        $subscription->user_id   =  $subscription->user_id;
        $subscription->subscription_id   =  $request->subscription_id;
        $subscription->subscription_type_id   =  $request->subscription_type_id;
        $subscription->subscription_plan_id   = $request->subscription_plan_id;
        $subscription->subscription_payment_id   =  $request->subscription_payment_id;
        $subscription->amount    =  $subscriptionPlan->amount;
        $subscription->type    =  strtolower(str_replace(' ', '-', $subscriptionPlan->type));

        // Set start and end dates
        $startDate = Carbon::now();
        $endDate = null;

        switch ($subscription->type) {
            case 'yearly':
                $endDate = $startDate->copy()->addYear();
                break;
            case 'monthly':
                $endDate = $startDate->copy()->addMonth();
                break;
            case 'lifetime':
                $endDate = null; // No end date for lifetime subscriptions
                break;
        }

        $subscription->status  =  ($request->payment_status == 'paid') ? 'active' : 'inactive';
        $subscription->payment_status    = $request->payment_status;

        $subscription->start_date = ($subscription->status == 'active') ? date('Y-m-d', strtotime($startDate)) : null;
        if ($endDate) {
            $subscription->end_date = ($subscription->status == 'active') ? date('Y-m-d', strtotime($endDate)) : null;
        } else {
            $subscription->end_date = null; // No end date for lifetime subscriptions
        }

        if ($subscription->save()) {
            return $subscription; // Return the updated subscription
        }

        return false; // Return false if the subscription could not be updated
    }

    public function createSubscriptionData($request, $user, $subscriptionPlan, $userSubscription)
    {
        if (!$subscriptionPlan) {
            return false;
        }
        $data = [
            'user_id'   =>  $user->id,
            'subscription_id'   =>  $request->subscription_id,
            'subscription_type_id'   =>  $request->subscription_type_id,
            'subscription_plan_id'   =>  $request->subscription_plan_id,
            'subscription_payment_id'   =>  $request->subscription_payment_id,
            'amount'    =>  $subscriptionPlan->amount,
            'type'    =>  strtolower(str_replace(' ', '-', $subscriptionPlan->type)),
            'status'    =>  'inactive',
            'payment_status' => 'unpaid'
        ];

        $userSubscription = $userSubscription->create($data);
        if ($userSubscription) {
            if (isset($request->status) && !empty($request->status)) {
                // Set start and end dates
                $startDate = Carbon::now();
                $endDate = null;

                switch ($userSubscription->type) {
                    case 'yearly':
                        $endDate = $startDate->copy()->addYear();
                        break;
                    case 'monthly':
                        $endDate = $startDate->copy()->addMonth();
                        break;
                    case 'lifetime':
                        $endDate = null; // No end date for lifetime subscriptions
                        break;
                }

                $userSubscription->status  =  ($request->payment_status == 'paid') ? 'active' : 'inactive';
                $userSubscription->payment_status    = $request->payment_status;

                $userSubscription->start_date = ($userSubscription->status == 'active') ? date('Y-m-d', strtotime($startDate)) : null;
                if ($endDate) {
                    $userSubscription->end_date = ($userSubscription->status == 'active') ? date('Y-m-d', strtotime($endDate)) : null;
                } else {
                    $userSubscription->end_date = null; // No end date for lifetime subscriptions
                }

                $userSubscription->save();
            }
        }
        return $userSubscription;
    }
}
