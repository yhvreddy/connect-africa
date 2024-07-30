<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;
use Illuminate\Support\Facades\Log;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Movies', 'slug' => 'movies', 'is_active' => true],
            ['title' => 'Shows', 'slug' => 'shows', 'is_active' => true],
            ['title' => 'Events', 'slug' => 'events', 'is_active' => true],
        ];

        foreach($categories as $data){
            $category = Categories::create($data);
            Log::info('Category Created : '.$category->title.' with Id: '.$category->id);
        }
    }
}
