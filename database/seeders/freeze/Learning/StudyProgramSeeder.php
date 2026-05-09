<?php

namespace Database\Seeders\Learning;

use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        StudyProgram::factory()->create([
            'title' => 'Discipleship',
            'slug' => 'discipleship',
            'description' => 'Learning path for discipleship growth',
            'status' => 'active',
        ]);

        StudyProgram::factory()->create([
            'title' => 'Sermon',
            'slug' => 'sermon',
            'description' => 'Learning path for sermon preparation and delivery',
            'status' => 'active',
        ]);
    }
}