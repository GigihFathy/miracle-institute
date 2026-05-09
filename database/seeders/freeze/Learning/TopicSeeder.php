<?php

namespace Database\Seeders\Learning;

use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $mentor = User::where('email', 'disciple@example.test')->firstOrFail();

        $topics = [
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'New Birth',
                'slug' => 'new-birth',
                'category' => 'Discipleship',
                'description' => 'Introduction to salvation and spiritual rebirth.',
                'poster' => 'images/dummyPNG.png',
            ],
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'Spiritual Disciplines',
                'slug' => 'spiritual-disciplines',
                'category' => 'Discipleship',
                'description' => 'Prayer, Bible study, and daily devotion habits.',
                'poster' => 'images/dummyPNG.png',
            ],
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'Serving the Church',
                'slug' => 'serving-the-church',
                'category' => 'Discipleship',
                'description' => 'Practical service and ministry involvement.',
                'poster' => 'images/dummyPNG.png',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Hermeneutics Basics',
                'slug' => 'hermeneutics-basics',
                'category' => 'Sermon',
                'description' => 'How to interpret a biblical text responsibly.',
                'poster' => 'images/dummyPNG.png',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Sermon Structure',
                'slug' => 'sermon-structure',
                'category' => 'Sermon',
                'description' => 'Building a sermon outline from introduction to application.',
                'poster' => 'images/dummyPNG.png',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Public Speaking for Ministry',
                'slug' => 'public-speaking-for-ministry',
                'category' => 'Sermon',
                'description' => 'Communication and delivery skills in ministry settings.',
                'poster' => 'images/dummyPNG.png',
            ],
        ];

        foreach ($topics as $index => $topic) {
            $course = Course::where('slug', $topic['course_slug'])->firstOrFail();

            Topic::factory()->create([
                'course_id' => $course->id,
                'teacher_id' => $mentor->id,
                'name' => $topic['name'],
                'slug' => $topic['slug'],
                'category' => $topic['category'],
                'description' => $topic['description'],
                'poster' => $topic['poster'],
                'visibility' => 'public',
                'status' => 'published',
                'sort_order' => $index + 1,
            ]);
        }
    }
}