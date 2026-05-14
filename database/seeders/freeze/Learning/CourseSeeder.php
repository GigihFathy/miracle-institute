<?php

namespace Database\Seeders\Learning;

use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $discipleship = StudyProgram::where('slug', 'discipleship')->firstOrFail();
        $sermon = StudyProgram::where('slug', 'sermon')->firstOrFail();

        Course::factory()->create([
            'study_program_id' => $discipleship->id,
            'title' => 'Foundational Discipleship',
            'slug' => 'foundational-discipleship',
            'poster' => 'images/dummyPNG.png',
            'credit' => 3,
            'description' => 'Core discipleship topics for new members and mentors',
            'status' => 'active',
        ]);

        Course::factory()->create([
            'study_program_id' => $sermon->id,
            'title' => 'Sermon Basics',
            'slug' => 'sermon-basics',
            'poster' => 'images/dummyPNG.png',
            'credit' => 4,
            'description' => 'Basic structure and delivery of sermons',
            'status' => 'active',
        ]);
    }
}