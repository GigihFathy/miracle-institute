<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE sessions MODIFY user_id CHAR(36) NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sessions ALTER COLUMN user_id TYPE UUID USING user_id::uuid');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sessions ALTER COLUMN user_id TYPE BIGINT USING NULL');
        }
    }
};
