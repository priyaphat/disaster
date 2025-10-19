<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2025_10_16_092214_add_emergency_request.php
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emergency_requests', function (Blueprint $t) {
            $t->string('other')->after('disaster_id')->nullable()->comment('อื่น ๆ จาก ความช่วยเหลือ');
        });

        Schema::table('disasters', function (Blueprint $t) {
            $t->tinyInteger('pointer')->after('image')->default(1)->comment('แบ่ง กลุ่ม');
        });
    }

};
