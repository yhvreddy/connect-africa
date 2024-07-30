<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_subscriptions', function (Blueprint $table) {
            $table->string('subscription_id', 250)->nullable();
            $table->string('subscription_status', 100)->nullable();
            $table->string('payment_status', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_subscriptions', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
            $table->dropColumn('subscription_status');
            $table->dropColumn('payment_status');
        });
    }
};
