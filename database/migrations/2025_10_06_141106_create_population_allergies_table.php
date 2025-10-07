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
        Schema::create('population_allergies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('population_id')->comment('id ของประชากร');
            $table->enum('status', [0, 1, 2])->default(0)->comment('0 - ไม่ทราบ, 1 - ไม่มี, 2 - แพ้ยา');
            $table->string('drug')->nullable();
            $table->string('food')->nullable();
            $table->string('other')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('population_id')->references('id')->on('populations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_allergies');
    }
};
