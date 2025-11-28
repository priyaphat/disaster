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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->softDeletes(); // This adds a nullable deleted_at column  

            $table->dropUnique('users_username_unique');
            $table->dropUnique('users_email_unique');
            $table->dropUnique('users_firstname_lastname_unique');

            $table->unique(['email', 'deleted_at']);
            $table->unique(['username', 'deleted_at']);
            $table->unique(['phone', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
};
