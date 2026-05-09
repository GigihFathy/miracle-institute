<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_user', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('course_id');
            $table->uuid('user_id');

            $table->enum('role_type', [
                'owner',
                'collaborator'
            ])->default('collaborator');

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->uuid('invited_by')->nullable();

            $table->timestamp('joined_at')->nullable();

            $table->timestamps();

            $table->unique([
                'course_id',
                'user_id'
            ]);

            $table->index('course_id');
            $table->index('user_id');
            $table->index('role_type');

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('invited_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};