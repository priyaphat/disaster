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
        Schema::create('appointment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parentable_id');      // id ของโมเดลที่ถูก comment (อาจเป็น user หรือโมเดลอื่นๆ)
            $table->string('parentable_type');                // ชื่อคลาส เช่น App\Models\User หรืออื่นๆ
            $table->text('description');                          // เนื้อหาคอมเมนต์
            $table->date('log_date')->index();            // วันที่เก็บข้อมูล (แยกตามวัน)
            $table->timestamps();
            // Index ช่วยค้นหาเร็ว
            $table->index(['parent_id', 'parent_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_logs');
    }
};
