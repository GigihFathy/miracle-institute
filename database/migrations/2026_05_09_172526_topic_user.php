<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('topic_user', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('topic_id');
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
                'topic_id',
                'user_id'
            ]);

            $table->index('topic_id');
            $table->index('user_id');
            $table->index('role_type');

            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
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
        Schema::dropIfExists('topic_user');
    }
};