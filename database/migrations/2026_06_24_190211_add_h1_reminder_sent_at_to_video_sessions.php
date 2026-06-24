<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_sessions', function (Blueprint $table) {
            $table->dateTime('h1_reminder_sent_at')->nullable()->after('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('video_sessions', function (Blueprint $table) {
            $table->dropColumn('h1_reminder_sent_at');
        });
    }
};
