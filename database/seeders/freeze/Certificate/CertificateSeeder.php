<?php

namespace Database\Seeders\Certificate;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $topicCounter = 1;
        $courseCounter = 1;

        $topicOrderByCourse = [
            'foundational-discipleship' => ['new-birth', 'spiritual-disciplines', 'serving-the-church'],
            'sermon-basics' => ['hermeneutics-basics', 'sermon-structure', 'public-speaking-for-ministry'],
        ];

        $users = User::whereIn('email', ['disciple@example.test', 'student@example.test'])->get();

        foreach ($users as $user) {
            foreach (TopicProgress::whereHas('courseEnrollment', fn ($q) => $q->where('user_id', $user->id))->get() as $progress) {
                if ($progress->status !== 'completed') {
                    continue;
                }

                $topic = Topic::find($progress->topic_id);
                $course = Course::find($progress->courseEnrollment->course_id);

                Certificate::factory()->create([
                    'certificate_number' => 'CERT-TOPIC-' . str_pad((string) $topicCounter++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'type' => 'topic',
                    'course_id' => $course?->id,
                    'topic_id' => $topic?->id,
                    'file_path' => 'certificates/topic/' . $user->email . '-' . $topic?->slug . '.pdf',
                    'issued_at' => now()->subDay(),
                    'status' => 'issued',
                ]);
            }

            foreach (CourseEnrollment::where('user_id', $user->id)->get() as $enrollment) {
                $course = Course::find($enrollment->course_id);
                $topicSlugs = $topicOrderByCourse[$course->slug] ?? [];

                $allCompleted = true;

                foreach ($topicSlugs as $slug) {
                    $topic = Topic::where('slug', $slug)->first();
                    $status = TopicProgress::where('course_enrollment_id', $enrollment->id)
                        ->where('topic_id', $topic?->id)
                        ->value('status');

                    if ($status !== 'completed') {
                        $allCompleted = false;
                        break;
                    }
                }

                if (!$allCompleted) {
                    continue;
                }

                Certificate::factory()->create([
                    'certificate_number' => 'CERT-COURSE-' . str_pad((string) $courseCounter++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'type' => 'course',
                    'course_id' => $course?->id,
                    'topic_id' => null,
                    'file_path' => 'certificates/course/' . $user->email . '-' . $course?->slug . '.pdf',
                    'issued_at' => now()->subDay(),
                    'status' => 'issued',
                ]);
            }
        }
    }
}