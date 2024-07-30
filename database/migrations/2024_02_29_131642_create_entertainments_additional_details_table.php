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
        Schema::create('entertainments_additional_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('entertainment_id');
            $table->string('type', 150);
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->unsignedInteger('em_id')->nullable();
            $table->foreign('entertainment_id')->references('id')->on('entertainments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('em_id')->references('id')->on('entertainments_master_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entertainments_additional_details');
    }
};
