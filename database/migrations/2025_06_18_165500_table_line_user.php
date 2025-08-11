<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //php artisan migrate --path=/database/migrations/2025_06_18_165500_table_line_user.php
    public function up(): void
    {
        Schema::create('line_users', function (Blueprint $table) {
            $table->id();
            $table->string('token_id')->unique();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('role')->nullable()->comment('1 user , 2 poppulation');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('line_users');
    }
};
