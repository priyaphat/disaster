<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users 
            ADD UNIQUE INDEX users_username_unique_no_null (username, (IFNULL(deleted_at, '0'))),
            ADD UNIQUE INDEX users_email_unique_no_null (email, (IFNULL(deleted_at, '0'))),
            ADD UNIQUE INDEX users_phone_unique_no_null (phone, (IFNULL(deleted_at, '0')))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE users 
            DROP INDEX users_email_deleted_at_unique,
            DROP INDEX users_username_deleted_at_unique,
            DROP INDEX users_firstname_lastname_deleted_at_unique
        ");
    }
};
