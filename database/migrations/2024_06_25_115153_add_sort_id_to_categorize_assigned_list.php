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
        Schema::table('categorize_assigned_list', function (Blueprint $table) {
            $table->integer('sort_id')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorize_assigned_list', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });
    }
};
