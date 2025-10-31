<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2025_10_19_221535_create_hospitals.php
     * 
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $q) {
            $q->id();
            $q->string('name');
            $q->tinyInteger('status')->default(1)->comment('0 inactive , 1 active');
            $q->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
