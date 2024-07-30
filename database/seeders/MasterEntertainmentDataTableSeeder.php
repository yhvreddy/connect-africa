<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;
use App\Models\EntertainmentMasterData;
use Illuminate\Support\Facades\Log;

class MasterEntertainmentDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Types Of Genres
        Log::debug("Genres List");
        $genres = [
            ['title' => 'Action', 'slug' => 'action', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Adventure', 'slug' => 'adventure', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Comedy', 'slug' => 'comedy', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Drama', 'slug' => 'drama', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Horror', 'slug' => 'horror', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Romance', 'slug' => 'romance', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Science fiction', 'slug' => 'science_fiction', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Fantasy', 'slug' => 'fantasy', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Historical', 'slug' => 'historical', 'type' => 'genres', 'is_active' => true],
            ['title' => 'Crime', 'slug' => 'crime', 'type' => 'genres', 'is_active' => true],
        ];

        foreach($genres as $data){
            $genre = EntertainmentMasterData::create($data);
            Log::info('Category Created : '.$genre->title.' with Id: '.$genre->id);
        }

        Log::debug("OTT Platform List");
        $ott_platforms = [
            ['title' => 'Netflix', 'slug' => 'netflix', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'Amazon Prime', 'slug' => 'amazon_prime', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'Disney +', 'slug' => 'disney_plus', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'iQIYI', 'slug' => 'iQIYI', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'HBO Max', 'slug' => 'hbo_max', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'Roku', 'slug' => 'roku', 'type' => 'ott_platforms', 'is_active' => true],
            ['title' => 'ESPN', 'slug' => 'espn', 'type' => 'ott_platforms', 'is_active' => true],
        ];

        foreach($ott_platforms as $data){
            $ott_platform = EntertainmentMasterData::create($data);
            Log::info('OTT Platform Created : '.$ott_platform->title.' with Id: '.$ott_platform->id);
        }

        Log::debug("Events Types List");
        $event_types = [
            ['title' => 'Sport Event', 'slug' => 'sport_event', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Magician', 'slug' => 'magician', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Circus acts', 'slug' => 'circus_acts', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Bands', 'slug' => 'bands', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Live music', 'slug' => 'live_music', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'DJ', 'slug' => 'dj', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Standup comedy', 'slug' => 'standup_comedy', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Dancers', 'slug' => 'dancers', 'type' => 'event_types', 'is_active' => true],
            ['title' => 'Musical Lights', 'slug' => 'musical_lights', 'type' => 'event_types', 'is_active' => true],
        ];
        foreach($event_types as $data){
            $event_type = EntertainmentMasterData::create($data);
            Log::info('Event Type Created : '.$event_type->title.' with Id: '.$event_type->id);
        }
    }
}
