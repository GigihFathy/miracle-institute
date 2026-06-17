<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'study_program_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('study_program_id');
            });
        }

        Schema::dropIfExists('study_programs');
    }

    public function down(): void
    {
        if (! Schema::hasTable('study_programs')) {
            Schema::create('study_programs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('courses', 'study_program_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignUuid('study_program_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('study_programs')
                    ->nullOnDelete();
            });
        }
    }
};
