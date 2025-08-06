<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path=/database/migrations/2025_07_26_002033_create_product_log_table.php
     */
    public function up(): void
    {
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('from')->nullable()->comment('from shelter');
            $table->unsignedInteger('to')->comment('to shelter');
            $table->json('detail')->nullable();
            $table->tinyInteger('type')->default(1)->comment('1 transfer 2 request 3 stop 4 receive');
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_logs');
    }
};
