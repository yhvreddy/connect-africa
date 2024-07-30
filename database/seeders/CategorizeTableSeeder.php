<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorize as CategorizeRepository;
use App\Http\Traits\TruFlix;

class CategorizeTableSeeder extends Seeder
{
    use TruFlix;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorize = new CategorizeRepository();
        $categorize->create([
            'title' => 'Trending Now',
            'slug' => $this->generateSlug('Trending Now'),
            'is_active' => true,
        ]);
        $categorize->create([
            'title' => 'Top 10 Movies',
            'slug' => $this->generateSlug('Top 10 Movies'),
            'is_active' => true,
        ]);
        $categorize->create([
            'title' => 'New Releases',
            'slug' => $this->generateSlug('New Releases'),
            'is_active' => true,
        ]);
        $categorize->create([
            'title' => 'Epic Worlds',
            'slug' => $this->generateSlug('Epic Worlds'),
            'is_active' => true,
        ]);
        $categorize->create([
            'title' => 'Blockbuster Action & Adventure',
            'slug' => $this->generateSlug('Blockbuster Action And Adventure'),
            'is_active' => true,
        ]);
    }
}
