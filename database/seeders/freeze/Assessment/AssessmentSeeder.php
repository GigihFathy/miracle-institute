<?php

namespace Database\Seeders\Assessment;

use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Course::all() as $course) {
            Assessment::factory()->create([
                'course_id' => $course->id,
                'title' => $course->name . ' Post Test',
            ]);
        }
    }
}