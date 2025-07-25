<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ตาราง products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('detail', 255)->nullable();
            $table->tinyInteger('unit_type')->default(1)->comment('1 Not_unit 2 Is_unit');
            $table->integer('status')->default(1)->comment('0 inactive 1 active');
            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('unit_id')->references('id')->on('units');
        });

        // ตาราง shelter_products
        Schema::create('shelter_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->double('quantity')->default(0);
            $table->unsignedBigInteger('shelter_id')->comment('0 center');
            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('product_id')->references('id')->on('products');
            // $table->foreign('shelter_id')->references('id')->on('shelters'); ต้องการใช้ -1 กับศูนย์กลาง
        });

        // ตาราง product_requests
        // Schema::create('product_requests', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('product_id');
        //     $table->integer('quantity')->nullable();
        //     $table->integer('no_quantity')->nullable();
        //     $table->unsignedBigInteger('shelter_id');
        //     $table->tinyInteger('status')->default(1)->comment('0 - ยุติคำขอ, 1 - รออนุมัติ, 2 - อนุมัติ');
        //     $table->timestamps();

        //     // Foreign keys (optional)
        //     $table->foreign('product_id')->references('id')->on('products');
        //     $table->foreign('shelter_id')->references('id')->on('shelters');
        // });
    }

    public function down(): void
    {
        // ลบตารางย้อนกลับตามลำดับที่ไม่ทำให้เกิดข้อผิดพลาด foreign key
        // Schema::dropIfExists('product_requests');
        Schema::dropIfExists('shelter_products');
        Schema::dropIfExists('products');
    }
};
