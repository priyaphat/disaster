<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=/database/migrations/2024_05_19_000000_create_emergency_request_table.php
     */
    public function up(): void
    {
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('prefix_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 10);
            $table->json('range_age')->nullable()->comment('["birth"=>"5","0-5"=>"2","60+"=>"1"]'); // array
            $table->json('victim_name')->nullable()->comment('[[prefix:int,fname:varchar,lname:varchar]]'); // array
            $table->unsignedInteger('disaster_id');
            $table->text('description')->nullable();
            $table->unsignedInteger('tambon_id');
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 - inactive , 1 - waiting , 2 - success');
            $table->unsignedInteger('emergency_team_id')->nullable()->comment('null=ยังไม่มีทีม');
            $table->unsignedInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};
