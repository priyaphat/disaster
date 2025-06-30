<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('populations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prefix_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->dateTime('birth_date');
            $table->unsignedTinyInteger('gender')->default(0)->comment('0 - ไม่ระบุเพศ, 1 - ชาย, 2 - หญิง');
            $table->string('phone', 10);
            $table->string('p_address', 100)->nullable();
            $table->unsignedBigInteger('p_geography_id')->nullable();
            $table->string('c_address', 100)->nullable();
            $table->unsignedBigInteger('c_geography_id')->nullable();
            $table->unsignedTinyInteger('same_address')->default(0);
            $table->unsignedTinyInteger('status')->default(2)->comment('0 - เสียชีวิต , 1 - บาดเจ็บ , 2 - ปกติ');
            $table->unsignedBigInteger('shelter_id')->nullable()->comment('หากออกจากศูนย์พักพิงแล้วจะเป็น null');
            $table->unsignedTinyInteger('tracking')->default(1)->comment('0 - ไม่ติดตาม , 1 - ติดตาม');
            $table->integer('sequence_id')->nullable()->comment('id ที่อ้างอิงจากการดึงข้อมูลจากภายนอก');
            $table->dateTime('created_date');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('populations');
    }
};
