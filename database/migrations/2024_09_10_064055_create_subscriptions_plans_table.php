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
        Schema::create('subscriptions_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_type_id')->unsigned();
            $table->string('name');
            $table->double('amount', 15, 2)->default(1.0);
            $table->string('type')->comment('yearly, monthly, lifetime');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscription_type_id')->references('id')->on('subscriptions_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions_plans');
    }
};
