<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('disaster_earthquakes', function (Blueprint $table) {
            $table->id();
            $table->string('address')->comment('ที่อยู่');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->float('depth')->comment('ความลึกของแผ่นดินไหว (กิโลเมตร)');
            $table->float('magnitude')->comment('ขนาดแผ่นดินไหว');
            $table->datetime('occurrence_time')->comment('เวลาที่เกิดแผ่นดินไหว');
            $table->unsignedBigInteger('geography_id')->nullable();
            $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_earthquakes');
    }
};
