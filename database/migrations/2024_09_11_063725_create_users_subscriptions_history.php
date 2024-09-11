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
        Schema::create('users_subscriptions_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_subscription_id')->unsigned();
            $table->integer('subscription_id')->unsigned();
            $table->integer('subscription_type_id')->unsigned();
            $table->integer('subscription_plan_id')->unsigned();
            $table->integer('subscription_payment_id')->unsigned();
            $table->double('amount', 15, 2)->nullable()->default(0.0);
            $table->string('type')->comment('yearly, monthly, lifetime, others');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('inactive')->commit('active, inactive');
            $table->string('payment_status')->default('pending')->commit('pending, paid, unpaid');
            $table->timestamps();

            $table->foreign('user_subscription_id')->references('id')->on('users_subscriptions');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('subscription_type_id')->references('id')->on('subscriptions_types');
            $table->foreign('subscription_plan_id')->references('id')->on('subscriptions_plans');
            $table->foreign('subscription_payment_id')->references('id')->on('subscriptions_payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_subscriptions_history');
    }
};
