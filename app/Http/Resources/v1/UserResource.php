<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subscription = $this->subscription;
        $subscriptionData = [];
        if ($subscription) {
            $subscriptionData = [
                'start_date'    =>  $subscription->start_date,
                'end_date'  =>  $subscription->end_date,
                'status'    =>  $subscription->status,
                'payment_status' => $subscription->payment_status
            ];
        }

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'mobile' => $this->mobile,
            'access_code' => $this->access_code,
            'disability_type' => $this->disability_type ?? '',
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'otp' => base64_encode($this->otp) . '!#!' . $this->otp,
            'country'   =>  $this->country?->name ?? null,
            'country_id'    =>  $this->country_id,
            'subscription_status' => $subscription?->isActive() ?? false,
        ];

        if (!isset($this->accessToken)) {
            $data['token'] = [
                'current_token' => request()->bearerToken(),
                'token_type' => 'Bearer',
                'encrypt_token' => $this->currentAccessToken()->token ?? null
            ];
        }

        if (isset($this->accessToken)) {
            $data['accessToken'] = $this->accessToken;
            $data['token_type'] = $this->token_type;
        }

        $data = array_merge($data, $subscriptionData);
        return $data;
    }
}
