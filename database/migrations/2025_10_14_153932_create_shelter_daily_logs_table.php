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
        Schema::create('shelter_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->date('log_date');
            $table->unsignedBigInteger('shelter_id')->comment('id ของศูนย์พักพิง');
            $table->tinyInteger('status')->comment('สถานะเปิดปิดของศูนย์พักพิง');
            $table->unsignedBigInteger('max_capacity');
            $table->unsignedBigInteger('current_capacity')->default(0);
            $table->unsignedBigInteger('total_victims')->default(0);
            $table->unsignedBigInteger('total_injuries')->default(0);
            $table->unsignedBigInteger('total_healthy')->default(0);
            $table->inteunsignedBigIntegerger('total_deaths')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['log_date', 'shelter_id']);

            // Foreign key constraints
            $table->foreign('shelter_id')->references('id')->on('shelters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelter_daily_logs');
    }
};
