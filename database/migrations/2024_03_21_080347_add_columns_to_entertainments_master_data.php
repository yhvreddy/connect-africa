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
        Schema::table('entertainments_master_data', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('other_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entertainments_master_data', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('other_response');
        });
    }
};
