<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('attempt_no')->default(1);

            $table->unsignedTinyInteger('passing_grade')->default(70);
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_answers')->default(0);
            $table->unsignedSmallInteger('wrong_answers')->default(0);
            $table->unsignedSmallInteger('unanswered_questions')->default(0);

            $table->unsignedTinyInteger('score')->nullable();
            $table->boolean('passed')->default(false);

            $table->enum('status', [
                'in_progress',
                'submitted',
                'auto_submitted',
                'abandoned',
            ])->default('in_progress');

            $table->json('question_snapshot')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();

            $table->unique(['assessment_id', 'user_id', 'attempt_no']);
            $table->index(['assessment_id', 'user_id']);
            $table->index(['user_id', 'passed']);
            $table->index(['user_id', 'status']);
            $table->index(['assessment_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_attempts');
    }
};