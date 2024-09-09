<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserForSupportaniaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'mobile' => $this->mobile,
            'avatar' => $this->avatar,
            'access_code' => $this->access_code,
            'territory' => $this->country->name ?? null,
            'country_id' => $this->country->id ?? null,
            'country' => $this->country->name ?? null,
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'otp' => base64_encode($this->otp).'!#!'.$this->otp,
            'stripe_customer_id'    =>  $this->stripe_customer_id,
            'password'              =>  $this->password,
            'subscription'          =>  [
                'payment'               =>  $this->subscription->payment_status ?? null,
                'subscription_status'   =>  $this->subscription->subscription_status ?? null,
                'customer_name'         =>  $this->subscription->customer_name ?? null,
                'ends_at'               =>  $this->subscription->ends_at ?? null,
            ]
        ];

        // if(!empty($this->subscription)){
        //     $subscription           = $this->subscription;
        //     $subscription->details  = json_decode($subscription->response_data);
        //     $data['user_subscription']   = $subscription;
        // }

        return $data;
    }
}
