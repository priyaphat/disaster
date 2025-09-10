<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('emergency_post_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('emergency_posts');
            $table->foreignId('disaster_id')->constrained('disasters');
            $table->integer('action')->comment('1 - create, 2 - update, 3 - delete'); // e.g., 'created', 'updated', 'deleted'
            $table->text('description')->nullable();
            $table->unsignedBigInteger('district')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_post_logs');
    }
};
