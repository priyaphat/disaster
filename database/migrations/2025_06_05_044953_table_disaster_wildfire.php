<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *  php artisan migrate --path=/database/migrations/2025_06_05_044953_table_disaster_wildfire.php
     */
    public function up(): void
    {
        Schema::create('disaster_wildfires', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->tinyInteger('type')->nullable()
                ->comment('1 - จุดความร้อน VIIRS, 2 5 จุดอันดับ พื้นที่ที่มีจุดความร้อน (เชียงราย)');
            $table->json('model');
            $table->string('host')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('disaster_wildfires');
    }
};
