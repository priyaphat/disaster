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
            $table->unsignedBigInteger('vulnerable_group_id')->nullable()->after('sequence_id')->comment('id ของกลุ่มเปราะบาง');
            $table->foreign('vulnerable_group_id')->references('id')->on('vulnerable_groups');
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
