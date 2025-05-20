<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //php artisan migrate --path=/database/migrations/2025_05_20_102651_create_disasters.php
    public function up(): void
    {
        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('น้ำท่วม,ไฟไหม้');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                //->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                //->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disasters');
    }
};
