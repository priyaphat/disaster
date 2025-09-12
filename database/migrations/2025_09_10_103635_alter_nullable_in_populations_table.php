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
        Schema::table('populations', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('status')->default(1)->comment('1 - ปกติ, 2 - บาดเจ็บ, 3 - เสียชีวิต, 4 - เปราะบาง')->change();
            $table->unsignedTinyInteger('tracking')->default(1)->comment('1 - ติดตาม, 2 - ไม่ติดตาม')->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('populations', function (Blueprint $table) {
            //
        });
    }
};
