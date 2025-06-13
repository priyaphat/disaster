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
        Schema::create('shelter_staffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unique('user_id', 'unique_user_id');
            $table->unsignedBigInteger('shelter_id')->nullable();
            $table->unsignedBigInteger('shelter_position_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('shelter_id')->references('id')->on('shelters');
            $table->foreign('shelter_position_id')->references('id')->on('shelter_positions');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelter_staffs');
    }
};
