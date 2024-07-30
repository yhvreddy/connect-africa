<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zq = Role::create([
            'title' => 'ZQ',
            'slug'  => 'zq'
        ]);

        $admin = Role::create([
            'title' => 'Admin',
            'slug'  => 'admin'
        ]);

        $partner = Role::create([
            'title' => 'Partner',
            'slug'  => 'partner'
        ]);
        
        $user = Role::create([
            'title' => 'User',
            'slug'  => 'user'
        ]);
        
        $affiliate = Role::create([
            'title' => 'Affiliate',
            'slug'  => 'affiliate'
        ]);

        Log::info("message: Role created successfully Id: ". $zq->title);
        Log::info("message: Role created successfully Id: ". $admin->title);
        Log::info("message: Role created successfully Id: ". $partner->title);
        Log::info("message: Role created successfully Id: ". $user->title);
        Log::info("message: Role created successfully Id: ". $affiliate->title);
    }
}
