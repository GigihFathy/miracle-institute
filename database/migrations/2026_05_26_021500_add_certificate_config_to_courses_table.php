<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('certificate_course_number')->nullable()->after('status');
            $table->string('certificate_prefix_code', 50)->nullable()->after('certificate_course_number');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_course_number',
                'certificate_prefix_code',
            ]);
        });
    }
};
