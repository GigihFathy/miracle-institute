<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignUuid('teacher_id')
                ->nullable()
                ->after('course_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};
