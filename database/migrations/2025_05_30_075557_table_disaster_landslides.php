<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disaster_landslides', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ชื่อดินถล่ม');
            $table->string('file_name')->nullable();
            $table->string('file_id')->nullable();
            $table->string('link')->nullable();
            $table->string('host')->nullable();
            $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('disaster_landslides');
    }
};
