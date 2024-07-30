<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\MoviesAdditionalResource;
use App\Http\Resources\v1\MoviesAdditionalCollection;


class EventsResource extends JsonResource
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
            'title' => $this->title,
            'slug' =>  $this->slug,
            'is_active' => $this->is_active,
            'poster_image' => $this->poster_path(true),
            'event_type' => $this->eventType,
            'description' => $this->description,
            'slug' => $this->slug,
            'date' => $this->date,
            'time' => $this->time,
            'category' => $this->category,
            'wp_one' => new MoviesAdditionalCollection($this->getAdditionalDataByType('movie_wp_one')->get()),
            'wp_two' => new MoviesAdditionalCollection($this->getAdditionalDataByType('movie_wp_two')->get()),
            'wp_three' => new MoviesAdditionalCollection($this->getAdditionalDataByType('movie_wp_three')->get()),
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'updated_at' => date('M d, Y', strtotime($this->updated_at)),
        ];

        return $data;
    }
}
