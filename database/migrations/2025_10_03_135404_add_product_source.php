<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2025_10_03_135404_add_product_source.php
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $q) {
            $q->string('source')->nullable()->after('detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {});
    }
};
