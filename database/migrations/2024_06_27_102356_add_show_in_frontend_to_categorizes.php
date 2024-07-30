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
        Schema::table('categorizes', function (Blueprint $table) {
            $table->integer('is_show_frontend')->after('is_in_menu')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorizes', function (Blueprint $table) {
            $table->dropColumn('is_show_frontend');
        });
    }
};
