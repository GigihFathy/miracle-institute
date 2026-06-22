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
            DB::statement('ALTER TABLE sessions MODIFY id VARCHAR(255) NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sessions ALTER COLUMN id TYPE VARCHAR(255)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE sessions MODIFY id CHAR(36) NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sessions ALTER COLUMN id TYPE UUID USING id::uuid');
        }
    }
};
