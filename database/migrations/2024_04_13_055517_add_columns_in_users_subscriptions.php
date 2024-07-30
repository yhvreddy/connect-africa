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
            $table->double('amount', 15, 8)->default(0.0);
            $table->double('tax_amount', 15, 8)->default(0.0);
            $table->double('total_amount', 15, 8)->default(0.0);
            $table->date('next_due_date')->nullable();
            $table->string('customer_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_subscriptions', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('tax_amount');
            $table->dropColumn('total_amount');
            $table->dropColumn('next_due_date');
            $table->dropColumn('customer_name');
        });
    }
};
