<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * php artisan migrate --path=/database/migrations/2025_11_27_115149_add_emergency_request.php
     */
    public function up(): void
    {
        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->tinyInteger('source')->default(1)->after('hospital_id')->comment('1-web , 2-line');
            $table->decimal('latitude', 10, 6)->nullable()->after('source');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('emergency_requests', function (Blueprint $table) {});
    }
};
