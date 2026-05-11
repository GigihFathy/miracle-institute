<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->enum('question_type', ['mcq'])->default('mcq'); // Hanya fokus MCQ
            $table->text('question');
            $table->text('correct_text_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['assessment_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};