<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('attendances')
            ->where('status', 'absent')
            ->update(['status' => 'online']);
    }

    public function down(): void
    {
        DB::table('attendances')
            ->where('status', 'online')
            ->whereNull('check_in_at')
            ->update(['status' => 'absent']);
    }
};
