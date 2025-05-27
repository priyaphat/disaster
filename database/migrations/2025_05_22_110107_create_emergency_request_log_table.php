<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2025_05_22_110107_create_emergency_request_log_table.php
     * php artisan tinker
     * Schema::dropIfExists('emergency_request_logs');
     */
    public function up(): void
    {
        Schema::create('emergency_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_request_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedInteger('reason_id');
            $table->string('reason')
                ->nullable()
                ->comment('เหตุผลอื่นๆ');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('emergency_request_logs');
    }
};
