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
        Schema::create('transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('parentable');
            $table->tinyInteger('action_type')->comment('1 - นำออกจากศูนย์ , 2 - ย้ายไปยังศูนย์ , 3 - เข้าสู่ศูนย์, 4 - รับหน้าที่, 5 - ย้ายออกจากทีม, 6 - ย้ายเข้าทีม');
            $table->morphs('childable');
            $table->string('childable_name')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_histories');
    }
};
