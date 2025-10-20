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
        Schema::create('shelter_daily_totals', function (Blueprint $table) {
            $table->id();
            $table->date('log_date');
            $table->unsignedBigInteger('total_shelter');
            $table->unsignedBigInteger('total_victims');
            $table->unsignedBigInteger('total_injuries');
            $table->unsignedBigInteger('total_healthy');
            $table->unsignedBigInteger('total_deaths');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelter_daily_totals');
    }
};
