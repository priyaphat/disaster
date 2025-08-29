<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path=/database/migrations/2025_08_26_135214_create_table_assessments.php
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('population_id')->nullable()->constrained('populations');
            $table->string('name', 255);
            $table->tinyInteger('type')->default(1)->comment('1 - assessment, 2 - inspection visit,  3- inspection child');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->json('detail')->nullable();
            $table->dateTime('datetime');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
