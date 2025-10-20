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
        Schema::create('population_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->date('log_date');
            $table->unsignedBigInteger('population_id')->comment('id ของประชากร');
            $table->unsignedBigInteger('shelter_id')->nullable()->comment('id ของศูนย์พักพิง');
            $table->tinyInteger('status')->comment('สถานะของประชากร');
            $table->tinyInteger('death_cause')->nullable()->comment('สาเหคุการเสียชีวิตของประชากร');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['log_date', 'population_id']);

            // Foreign key constraints
            $table->foreign('population_id')->references('id')->on('populations');
            $table->foreign('shelter_id')->references('id')->on('shelters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_daily_logs');
    }
};
