<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\TruFlix;
class UserSeeder extends Seeder
{
    use TruFlix;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zq = User::create([
            'name' => 'ZQ',
            'email' => 'zq@truflix.com',
            'username' => 'zqTruflix',
            'password' => Hash::make('admin@123!'),
            'email_verified_at' => now(),
            'role_id'   =>  1
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@truflix.com',
            'username' => 'adminTruflix',
            'password' => Hash::make('admin@123!'),
            'email_verified_at' => now(),
            'role_id'   =>  2
        ]);
        
        $partner = User::create([
            'name' => 'Partner',
            'email' => 'partner@truflix.com',
            'username' => 'partnerTruflix',
            'password' => Hash::make('admin@123!'),
            'email_verified_at' => now(),
            'role_id'   =>  3
        ]);
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@truflix.com',
            'username' => 'userTruflix',
            'password' => Hash::make('admin@123!'),
            'email_verified_at' => now(),
            'role_id'   =>  4
        ]);

        Log::info("message: ".$zq->name." created successfully Id: ". $zq->id);
        Log::info("message: ".$admin->name." created successfully Id: ". $admin->id);
        Log::info("message: ".$partner->name." created successfully Id: ". $partner->id);
        Log::info("message: ".$user->name." created successfully Id: ". $user->id);
    }
}
