<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;

class SiteSettingsResource extends JsonResource
{
    use HttpResponses, TruFlix;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'copyrights' => $this->copyrights,
            'logo' => !empty($this->logo)?$this->getFilePath($this->logo, true):null,
            'fav_icon' => !empty($this->fav_icon)?$this->getFilePath($this->fav_icon, true):null,
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'updated_at' => date('M d, Y', strtotime($this->updated_at)),
        ];

        if(isset($this->socialMediaDetails)){
            $data['social_media'] = $this->socialMediaDetails;
        }

        return $data;
    }
}
