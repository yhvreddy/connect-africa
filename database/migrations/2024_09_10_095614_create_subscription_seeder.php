<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\SubscriptionSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Call the seeder
        $seeder = new SubscriptionSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
