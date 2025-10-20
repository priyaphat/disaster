<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2025_10_16_101451_create_emergency_teams.php
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emergency_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emc_id')->constrained('emergency_requests');
            $table->foreignId('team_id')->constrained('teams');
            $table->integer('seq_no')->comment('sequent NO.');
            $table->timestamps();
        });

        Schema::table('emergency_requests', function (Blueprint $q) {
            $q->dropColumn('emergency_team_id');
            $q->foreignId('accept_by')->nullable()->constrained('users')->after('emergency_team_name');
            $q->foreignId('hospital_id')->nullable()->constrained('hospitals')->after('accept_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->foreignId('emergency_team_id')->nullable()->constrained('emergency_teams');
        });

        Schema::dropIfExists('emergency_teams');
    }
};
