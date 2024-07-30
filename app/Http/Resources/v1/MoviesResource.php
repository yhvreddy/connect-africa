<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\MoviesAdditionalResource;
use App\Http\Resources\v1\MoviesAdditionalCollection;
use Illuminate\Support\Facades\Storage;

class MoviesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $movie_wp_one = $this->getAdditionalDataByType('movie_wp_one')->get();
        foreach($movie_wp_one as $value){
            $image = null;
            if(!empty($value->master->image) && Storage::exists($value->master->image)){
                $image = asset(Storage::url($value->master->image));
            }
            $value->master->image = $image;
        }


        $movie_wp_two = $this->getAdditionalDataByType('movie_wp_two')->get();
        foreach($movie_wp_two as $value){
            $image = null;
            if(!empty($value->master->image) && Storage::exists($value->master->image)){
                $image = asset(Storage::url($value->master->image));
            }
            $value->master->image = $image;
        }

        $movie_wp_three = $this->getAdditionalDataByType('movie_wp_three')->get();
        foreach($movie_wp_three as $value){
            $image = null;
            if(!empty($value->master->image) && Storage::exists($value->master->image)){
                $image = asset(Storage::url($value->master->image));
            }
            $value->master->image = $image;
        }

        $movie_free_option = $this->getAdditionalDataByType('movie_free_option')->get();
        foreach($movie_free_option as $value){
            $image = null;
            if(!empty($value->master->image) && Storage::exists($value->master->image)){
                $image = asset(Storage::url($value->master->image));
            }
            $value->master->image = $image;
        }

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' =>  $this->slug,
            'is_active' => $this->is_active,
            'poster_image' => $this->poster_path(true),
            'event_type_id' => $this->event_type_id,
            'year' => $this->year,
            'rated' => $this->rating,
            'description' => $this->description,
            'platform' => [
                [
                    'name'  => 'IMDB',
                    'slug'  =>  'imdb',
                    'score' => $this->imdb_score
                ],[
                    'name'  => 'Google',
                    'slug'  =>  'google',
                    'score' => $this->google_score
                ],[
                    'name'  => 'Rotten Tomatoes',
                    'slug'  =>  'rotten',
                    'score' => $this->rt_score
                ],[
                    'name'  =>  'Truflix Score',
                    'slug'  =>  'truflix',
                    'score' =>  $this->truflix_score
                ]
            ],
            'slug' => $this->slug,
            'date' => $this->date,
            'time' => $this->time,
            'category' => $this->category,
            'videos' => new MoviesAdditionalCollection($this->getAdditionalDataByType('movies_video')->get()),
            'watch' => [
                'wp_one' => new MoviesAdditionalCollection($movie_wp_one),
                'wp_two' => new MoviesAdditionalCollection($movie_wp_two),
                'wp_three' => new MoviesAdditionalCollection($movie_wp_three),
                'free_option' => new MoviesAdditionalCollection($movie_free_option),
            ],
            'created_at' => date('M d, Y', strtotime($this->created_at)),
            'updated_at' => date('M d, Y', strtotime($this->updated_at)),
        ];

        return $data;
    }
}
