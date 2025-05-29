<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emergency_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disaster_id');
            $table->tinyInteger('level');
            $table->unsignedBigInteger('province_id');
            // $table->json('area');
            $table->text('gis_map')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 - inactive, 1 - active');
            $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
            // $table->unsignedBigInteger('history_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('disaster_id')->references('id')->on('disasters');
            // $table->foreign('history_id')->references('id')->on('emergency_posts')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_posts');
    }
};
