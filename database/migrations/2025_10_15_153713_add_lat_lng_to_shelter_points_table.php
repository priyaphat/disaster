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
        Schema::table('shelter_points', function (Blueprint $table) {
            //
            $table->decimal('latitude', 10, 6)->nullable()->after('map_url');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelter_points', function (Blueprint $table) {
            //
        });
    }
};
