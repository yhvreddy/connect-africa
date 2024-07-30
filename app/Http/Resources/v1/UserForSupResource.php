<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserForSupResource extends JsonResource
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
            'country_id' => $this->country->id ?? null,
            'country' => $this->country->name ?? null,
            'stripe_id'           =>  $this->stripe_customer_id ?? null,
            'subscription'        =>  $this->subscription->subscription_status ?? 'inactive',
        ];


        if(isset($this->send_password) && $this->send_password){
            $data['password']   = $this->password;
        }

        return $data;
    }
}
