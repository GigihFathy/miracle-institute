<?php

namespace Database\Seeders\Learning;

use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $topicOrderByCourse = [
            'foundational-discipleship' => ['new-birth', 'spiritual-disciplines', 'serving-the-church'],
            'sermon-basics' => ['hermeneutics-basics', 'sermon-structure', 'public-speaking-for-ministry'],
        ];

        $completionMatrix = [
            'disciple@example.test' => [
                'foundational-discipleship' => 3,
                'sermon-basics' => 3,
            ],
            'student@example.test' => [
                'foundational-discipleship' => 3,
                'sermon-basics' => 3,
            ],
        ];

        foreach (User::where('email', '!=', 'admin@example.test')->get() as $user) {
            foreach (CourseEnrollment::where('user_id', $user->id)->get() as $enrollment) {
                $courseSlug = $enrollment->course->slug;
                $topicSlugs = $topicOrderByCourse[$courseSlug] ?? [];
                $completedCount = $completionMatrix[$user->email][$courseSlug] ?? 0;

                foreach ($topicSlugs as $index => $topicSlug) {
                    $topic = Topic::where('slug', $topicSlug)->firstOrFail();
                    $status = $index < $completedCount
                        ? 'completed'
                        : ($index === $completedCount ? 'in_progress' : 'not_started');

                    TopicProgress::updateOrCreate(
                        [
                            'course_enrollment_id' => $enrollment->id,
                            'topic_id' => $topic->id,
                        ],
                        [
                            'status' => $status,
                            'started_at' => $status !== 'not_started' ? now()->subDays(7 - $index) : null,
                            'completed_at' => $status === 'completed' ? now()->subDays(3 - $index) : null,
                        ]
                    );

                    $materials = Material::where('topic_id', $topic->id)->get();

                    foreach ($materials as $mIndex => $material) {
                        MaterialProgress::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'material_id' => $material->id,
                            ],
                            [
                                'status' => $status === 'completed'
                                    ? 'completed'
                                    : ($mIndex === 0 ? 'viewed' : 'not_started'),
                                'started_at' => $status !== 'not_started' ? now()->subDays(5) : null,
                                'completed_at' => $status === 'completed' ? now()->subDays(2) : null,
                            ]
                        );
                    }
                }
            }
        }
    }
}