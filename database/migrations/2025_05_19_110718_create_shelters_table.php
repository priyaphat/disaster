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
        Schema::create('shelters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone')->nullable();
            $table->integer('max_capacity'); // not null
            $table->unsignedBigInteger('geography_id');
            $table->integer('province_code');
            $table->integer('district_code');
            $table->integer('subdistrict_code');
            $table->string('address', 255)->nullable();
            $table->tinyInteger('status')
                ->default(2)
                ->comment('2 - inactive , 1 - active');
            $table->timestamp('created_date')
                ->comment('วันก่อตั้ง');
            $table->json('utilities')
                ->nullable()->comment('1 = น้ำประปา, 2 = ไฟฟ้า, 3 = บำบัดน้ำเสีย, 4 = สุขาภิบาล (ห้องน้ำ), 5 = การกำจัดขยะ, 6 = สัญญาณอินเทอร์เน็ต');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();

            // $table->foreign('geography_id')->references('id')->on('geography');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelters');
    }
};
