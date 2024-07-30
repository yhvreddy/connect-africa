<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'access_code' => $this->access_code,
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'otp' => base64_encode($this->otp).'!#!'.$this->otp
        ];

        if(!isset($this->accessToken)){
            $data['token'] = [
                'current_token' => request()->bearerToken(),
                'token_type' => 'Bearer',
                'encrypt_token' => $this->currentAccessToken()->token ?? null
            ];
        }

        if(isset($this->accessToken)){
            $data['accessToken'] = $this->accessToken;
            $data['token_type'] = $this->token_type;
        }

        return $data;
    }
}
