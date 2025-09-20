<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('disaster_smogs', function (Blueprint $table) {
            $table->id();
            $table->string('source')->comment('แหล่งที่มาของหมอกควัน');
            $table->string('address')->comment('ที่อยู่');
            $table->integer('status')->default(0)->comment('รอข้อมูลอยู่');
            $table->decimal('smog_value', 6, 2)->comment('ค่าหมอกควัน');
            $table->unsignedBigInteger('geography_id')->nullable();
            $table->string('host')->nullable();
            $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_smogs');
    }
};
