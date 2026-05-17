<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('topic_id')
                ->constrained('topics')
                ->cascadeOnDelete();

            $table->foreignUuid('uploader_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name')->default('Unnamed File');
            $table->string('type'); // video, pdf, ppt
            $table->string('path')->nullable();
            $table->string('external_url')->nullable();

            $table->string('visibility')->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};