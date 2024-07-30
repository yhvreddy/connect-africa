<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MoviesAdditionalResource extends JsonResource
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
            'type' => $this->type,
            'title' => $this->title,
            'image' => $this->image_path(true),
            'description' => $this->description,
            'url' => $this->url,
            'master' => !empty($this->em_id)?$this->master:null,
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'updated_at' => date('M d, Y', strtotime($this->updated_at)),
        ];

        return $data;
    }
}
