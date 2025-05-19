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
            $table->integer('phone')->nullable();
            $table->integer('max_capacity'); // not null
            $table->string('address', 255)->nullable();
            $table->tinyInteger('status')
                  ->default(1)
                  ->comment('0 - inactive , 1 - active');
            $table->timestamp('created_date')
                  ->comment('วันก่อตั้ง');
            $table->unsignedBigInteger('leader_id'); // not null
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
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
