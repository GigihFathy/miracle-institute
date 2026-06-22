<?php

namespace Database\Seeders\Learning;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::factory()->create([
            'title' => 'Foundational Discipleship',
            'slug' => 'foundational-discipleship',
            'poster' => 'images/dummyPNG.png',
            'description' => 'Core discipleship topics for new members and mentors',
            'status' => 'active',
        ]);

        Course::factory()->create([
            'title' => 'Sermon Basics',
            'slug' => 'sermon-basics',
            'poster' => 'images/dummyPNG.png',
            'description' => 'Basic structure and delivery of sermons',
            'status' => 'active',
        ]);
    }
}
