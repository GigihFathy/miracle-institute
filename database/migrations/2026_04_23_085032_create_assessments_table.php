<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->string('title');
            $table->unsignedTinyInteger('passing_grade')->default(70);
            $table->boolean('randomize_questions')->default(false);
            $table->unsignedSmallInteger('question_limit')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->string('status')->default('active'); //'draft', 'active', 'archived'

            $table->timestamps();

            $table->unique('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};