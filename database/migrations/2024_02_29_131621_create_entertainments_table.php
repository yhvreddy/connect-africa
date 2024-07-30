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
        Schema::create('entertainments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('event_type_id')->nullable()->unsigned();
            $table->string('title');
            $table->string('year')->nullable();
            $table->string('rating')->nullable();
            $table->text('description')->nullable();
            $table->string('imdb_score')->nullable();
            $table->string('google_score')->nullable();
            $table->string('rt_score')->nullable()->comment('Rotten Tomatoes Score');
            $table->string('poster_image')->nullable();
            $table->string('slug')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade');
            $table->foreign('event_type_id')->references('id')->on('entertainments_master_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entertainments');
    }
};
