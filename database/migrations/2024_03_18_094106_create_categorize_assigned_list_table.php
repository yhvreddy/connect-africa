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
        Schema::create('categorize_assigned_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categorize_id')->unsigned();
            $table->integer('entertainment_id')->unsigned();
            $table->string('type', 100)->default('movies')->comment('movies, shows, etc..');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorize_assigned_list');
    }
};
