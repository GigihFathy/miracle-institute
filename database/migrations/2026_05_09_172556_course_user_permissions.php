<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_user_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('course_user_id');

            $table->string('permission');

            $table->uuid('granted_by')->nullable();

            $table->timestamps();

            $table->unique([
                'course_user_id',
                'permission'
            ]);

            $table->index('course_user_id');
            $table->index('permission');

            $table->foreign('course_user_id')
                ->references('id')
                ->on('course_user')
                ->cascadeOnDelete();

            $table->foreign('granted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_user_permissions');
    }
};