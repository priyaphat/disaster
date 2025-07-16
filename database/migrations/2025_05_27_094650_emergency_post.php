<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *  php artisan migrate --path=/database/migrations/2025_05_27_094650_emergency_post.php
     */
    public function up()
    {
        Schema::create('emergency_posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('disaster_id');
            $table->tinyInteger('level');
            $table->unsignedBigInteger('province_id');
            $table->text('gis_map')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 - inactive, 1 - active');
            $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('disaster_id')->references('id')->on('disasters');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_posts');
    }
};
