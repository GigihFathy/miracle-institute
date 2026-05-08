<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'role_user',
            'roles',
            'assessment_answers',
            'assessment_attempts',
            'question_options',
            'questions',
            'assessments',
            'certificates',
            'attendances',
            'material_progresses',
            'topic_progresses',
            'course_enrollments',
            'materials',
            'sessions',
            'topics',
            'courses',
            'study_programs',
            'user_views',
            'user_documents',
            'articles',
            'article_images',
            'sliders',
            'tutorial_videos',
            'companies',
            'users',
            'password_resets',
            'failed_jobs',
            'jobs',
            'personal_access_tokens',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        $roles = $this->seedRoles($now);
        $users = $this->seedUsers($roles, $now);

        $studyPrograms = $this->seedStudyPrograms($now);
        $courses = $this->seedCourses($studyPrograms, $now);
        $topics = $this->seedTopics($courses, $users, $now);
        $sessions = $this->seedSessions($topics, $now);
        $materials = $this->seedMaterials($topics, $users, $now);

        $enrollments = $this->seedCourseEnrollments($users, $courses, $now);
        $topicProgresses = $this->seedTopicProgresses($enrollments, $topics, $now);
        $this->seedMaterialProgresses($users, $materials, $topicProgresses, $now);
        $this->seedUserViews($users, $topics, $topicProgresses, $now);
        $this->seedAttendances($users, $sessions, $now);

        $assessments = $this->seedAssessments($topics, $now);
        $assessmentQuestions = $this->seedQuestionsAndOptions($assessments, $now);
        $this->seedAssessmentAttemptsAndAnswers($users, $assessments, $assessmentQuestions, $topicProgresses, $now);

        $this->seedCertificates($users, $courses, $topics, $topicProgresses, $enrollments, $now);

        $this->seedArticles($now);
        $this->seedCompany($now);
        $this->seedSliders($now);
        $this->seedTutorialVideos($now);
        $this->seedUserDocuments($users, $now);
    }

    private function uuid(): string
    {
        return (string) Str::uuid();
    }

    private function seedRoles(Carbon $now): array
    {
        $rows = [
            [
                'id' => $this->uuid(),
                'name' => 'admin',
                'label' => 'Admin',
                'description' => 'Full system access',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'name' => 'student',
                'label' => 'Student',
                'description' => 'Learner role',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'name' => 'disciples',
                'label' => 'Disciples',
                'description' => 'Mentor / tutor role',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('roles')->insert($rows);

        return array_column($rows, 'id', 'name');
    }

    private function seedUsers(array $roles, Carbon $now): array
    {
        $users = [
            'admin' => [
                'name' => 'System',
                'name' => 'Admin',
                'email' => 'admin@example.test',
                'password' => Hash::make('password'),
                'phone' => '081111111111',
                'gender' => 'Male',
                'dob' => '1990-01-01',
                'image' => 'users/admin.jpg',
            ],
            'mentor' => [
                'name' => 'Grace',
                'name' => 'Mentor',
                'email' => 'mentor@example.test',
                'password' => Hash::make('password'),
                'phone' => '081111111112',
                'gender' => 'Female',
                'dob' => '1992-02-02',
                'image' => 'users/mentor.jpg',
            ],
            'student1' => [
                'name' => 'Alex',
                'name' => 'Johnson',
                'email' => 'student1@example.test',
                'password' => Hash::make('password'),
                'phone' => '081111111113',
                'gender' => 'Male',
                'dob' => '2000-03-03',
                'image' => 'users/student1.jpg',
            ],
            'student2' => [
                'name' => 'Bella',
                'name' => 'Smith',
                'email' => 'student2@example.test',
                'password' => Hash::make('password'),
                'phone' => '081111111114',
                'gender' => 'Female',
                'dob' => '2001-04-04',
                'image' => 'users/student2.jpg',
            ],
            'student3' => [
                'name' => 'Chris',
                'name' => 'Lee',
                'email' => 'student3@example.test',
                'password' => Hash::make('password'),
                'phone' => '081111111115',
                'gender' => 'Male',
                'dob' => '2002-05-05',
                'image' => 'users/student3.jpg',
            ],
        ];

        $rows = [];
        $ids = [];

        foreach ($users as $key => $user) {
            $ids[$key] = $this->uuid();

            $rows[] = [
                'id' => $ids[$key],
                'name' => $user['name'],
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => $now->copy()->subDays(30),
                'password' => $user['password'],
                'phone' => $user['phone'],
                'gender' => $user['gender'],
                'dob' => $user['dob'],
                'image' => $user['image'],
                'remember_token' => Str::random(10),
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($rows);

        $roleUserRows = [
            [
                'id' => $this->uuid(),
                'user_id' => $ids['admin'],
                'role_id' => $roles['admin'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'user_id' => $ids['mentor'],
                'role_id' => $roles['student'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'user_id' => $ids['mentor'],
                'role_id' => $roles['disciples'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'user_id' => $ids['student1'],
                'role_id' => $roles['student'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'user_id' => $ids['student2'],
                'role_id' => $roles['student'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'user_id' => $ids['student3'],
                'role_id' => $roles['student'],
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('role_user')->insert($roleUserRows);

        return $ids;
    }

    private function seedStudyPrograms(Carbon $now): array
    {
        $rows = [
            [
                'id' => $this->uuid(),
                'title' => 'Discipleship',
                'slug' => 'discipleship',
                'description' => 'Learning path for discipleship growth',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'title' => 'Sermon',
                'slug' => 'sermon',
                'description' => 'Learning path for sermon preparation and delivery',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('study_programs')->insert($rows);

        return array_column($rows, 'id', 'slug');
    }

    private function seedCourses(array $studyPrograms, Carbon $now): array
    {
        $rows = [
            [
                'id' => $this->uuid(),
                'study_program_id' => $studyPrograms['discipleship'],
                'title' => 'Foundational Discipleship',
                'slug' => 'foundational-discipleship',
                'poster' => 'courses/foundational-discipleship.jpg',
                'credit' => 3,
                'description' => 'Core discipleship topics for new members and mentors',
                'quota' => 50,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'study_program_id' => $studyPrograms['sermon'],
                'title' => 'Sermon Basics',
                'slug' => 'sermon-basics',
                'poster' => 'courses/sermon-basics.jpg',
                'credit' => 4,
                'description' => 'Basic structure and delivery of sermons',
                'quota' => 40,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('courses')->insert($rows);

        return array_column($rows, 'id', 'slug');
    }

    private function topicBlueprints(): array
    {
        return [
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'New Birth',
                'slug' => 'new-birth',
                'category' => 'Discipleship',
                'description' => 'Introduction to salvation and spiritual rebirth.',
                'poster' => 'topics/new-birth.jpg',
            ],
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'Spiritual Disciplines',
                'slug' => 'spiritual-disciplines',
                'category' => 'Discipleship',
                'description' => 'Prayer, Bible study, and daily devotion habits.',
                'poster' => 'topics/spiritual-disciplines.jpg',
            ],
            [
                'course_slug' => 'foundational-discipleship',
                'name' => 'Serving the Church',
                'slug' => 'serving-the-church',
                'category' => 'Discipleship',
                'description' => 'Practical service and ministry involvement.',
                'poster' => 'topics/serving-the-church.jpg',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Hermeneutics Basics',
                'slug' => 'hermeneutics-basics',
                'category' => 'Sermon',
                'description' => 'How to interpret a biblical text responsibly.',
                'poster' => 'topics/hermeneutics-basics.jpg',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Sermon Structure',
                'slug' => 'sermon-structure',
                'category' => 'Sermon',
                'description' => 'Building a sermon outline from introduction to application.',
                'poster' => 'topics/sermon-structure.jpg',
            ],
            [
                'course_slug' => 'sermon-basics',
                'name' => 'Public Speaking for Ministry',
                'slug' => 'public-speaking-for-ministry',
                'category' => 'Sermon',
                'description' => 'Communication and delivery skills in ministry settings.',
                'poster' => 'topics/public-speaking-for-ministry.jpg',
            ],
        ];
    }

    private function seedTopics(array $courses, array $users, Carbon $now): array
    {
        $rows = [];
        $map = [];

        foreach ($this->topicBlueprints() as $index => $topic) {
            $id = $this->uuid();

            $row = [
                'id' => $id,
                'course_id' => $courses[$topic['course_slug']],
                'teacher_id' => $users['mentor'],
                'name' => $topic['name'],
                'slug' => $topic['slug'],
                'category' => $topic['category'],
                'description' => $topic['description'],
                'poster' => $topic['poster'],
                'visibility' => 'public',
                'status' => 'published',
                'sort_order' => $index + 1,
                'created_at' => $now->copy()->subDays(21),
                'updated_at' => $now,
            ];

            $rows[] = $row;
            $map[$topic['slug']] = [
                'id' => $id,
                'course_slug' => $topic['course_slug'],
                'course_id' => $courses[$topic['course_slug']],
                'name' => $topic['name'],
                'slug' => $topic['slug'],
            ];
        }

        DB::table('topics')->insert($rows);

        return $map;
    }

    private function seedSessions(array $topics, Carbon $now): array
    {
        $rows = [];
        $map = [];

        $topicOrder = [
            'new-birth',
            'spiritual-disciplines',
            'serving-the-church',
            'hermeneutics-basics',
            'sermon-structure',
            'public-speaking-for-ministry',
        ];

        foreach ($topicOrder as $index => $slug) {
            $startAt = $now->copy()->addDays(($index + 1) * 2)->setTime(19, 0, 0);
            $endAt = $startAt->copy()->addHour();

            $id = $this->uuid();

            $rows[] = [
                'id' => $id,
                'topic_id' => $topics[$slug]['id'],
                'title' => 'Session for ' . $topics[$slug]['name'],
                'zoom_link' => 'https://zoom.us/j/1234567890?topic=' . $slug,
                'record_link' => 'https://youtube.com/watch?v=record-' . $slug,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'reminder_sent_at' => $startAt->copy()->subHour(),
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $map[$slug] = [
                'id' => $id,
                'topic_id' => $topics[$slug]['id'],
                'start_at' => $startAt,
                'end_at' => $endAt,
            ];
        }

        DB::table('sessions')->insert($rows);

        return $map;
    }

    private function seedMaterials(array $topics, array $users, Carbon $now): array
    {
        $rows = [];
        $map = [];

        $typeTemplates = [
            [
                'type' => 'video',
                'name_suffix' => 'Teaching Video',
                'path' => null,
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'type' => 'pdf',
                'name_suffix' => 'Lesson Notes',
                'path' => null,
                'external_url' => 'https://example.test/files/lesson-notes.pdf',
            ],
            [
                'type' => 'ppt',
                'name_suffix' => 'Presentation Slides',
                'path' => null,
                'external_url' => 'https://example.test/files/presentation-slides.pptx',
            ],
        ];

        $topicSlugs = [
            'new-birth',
            'spiritual-disciplines',
            'serving-the-church',
            'hermeneutics-basics',
            'sermon-structure',
            'public-speaking-for-ministry',
        ];

        foreach ($topicSlugs as $topicIndex => $slug) {
            foreach ($typeTemplates as $materialIndex => $template) {
                $id = $this->uuid();

                $rows[] = [
                    'id' => $id,
                    'topic_id' => $topics[$slug]['id'],
                    'uploader_id' => $users['mentor'],
                    'name' => $topics[$slug]['name'] . ' - ' . $template['name_suffix'],
                    'type' => $template['type'],
                    'path' => $template['path'],
                    'external_url' => $template['external_url'],
                    'visibility' => 'public',
                    'sort_order' => $materialIndex + 1,
                    'status' => 'active',
                    'created_at' => $now->copy()->subDays(14),
                    'updated_at' => $now,
                ];

                $map[$slug][] = [
                    'id' => $id,
                    'type' => $template['type'],
                    'name' => $topics[$slug]['name'] . ' - ' . $template['name_suffix'],
                ];
            }
        }

        DB::table('materials')->insert($rows);

        return $map;
    }

    private function seedCourseEnrollments(array $users, array $courses, Carbon $now): array
    {
        $rows = [];
        $map = [];

        $enrolledUsers = ['mentor', 'student1', 'student2', 'student3'];

        foreach ($enrolledUsers as $userKey) {
            foreach (array_keys($courses) as $courseSlug) {
                $id = $this->uuid();

                $rows[] = [
                    'id' => $id,
                    'user_id' => $users[$userKey],
                    'course_id' => $courses[$courseSlug],
                    'status' => 'active',
                    'enrolled_at' => $now->copy()->subDays(10),
                    'completed_at' => null,
                    'created_at' => $now->copy()->subDays(10),
                    'updated_at' => $now,
                ];

                $map[$userKey][$courseSlug] = $id;
            }
        }

        DB::table('course_enrollments')->insert($rows);

        return $map;
    }

    private function seedTopicProgresses(array $enrollments, array $topics, Carbon $now): array
    {
        $rows = [];
        $map = [];

        $topicOrderByCourse = [
            'foundational-discipleship' => [
                'new-birth',
                'spiritual-disciplines',
                'serving-the-church',
            ],
            'sermon-basics' => [
                'hermeneutics-basics',
                'sermon-structure',
                'public-speaking-for-ministry',
            ],
        ];

        $completionMatrix = [
            'mentor' => [
                'foundational-discipleship' => 3,
                'sermon-basics' => 3,
            ],
            'student1' => [
                'foundational-discipleship' => 3,
                'sermon-basics' => 3,
            ],
            'student2' => [
                'foundational-discipleship' => 2,
                'sermon-basics' => 2,
            ],
            'student3' => [
                'foundational-discipleship' => 1,
                'sermon-basics' => 1,
            ],
        ];

        foreach ($enrollments as $userKey => $courses) {
            foreach ($courses as $courseSlug => $enrollmentId) {
                $completedCount = $completionMatrix[$userKey][$courseSlug] ?? 0;
                $topicSlugs = $topicOrderByCourse[$courseSlug] ?? [];

                foreach ($topicSlugs as $index => $topicSlug) {
                    $status = $index < $completedCount ? 'completed' : ($index === $completedCount ? 'in_progress' : 'not_started');

                    $rowId = $this->uuid();

                    $rows[] = [
                        'id' => $rowId,
                        'course_enrollment_id' => $enrollmentId,
                        'topic_id' => $topics[$topicSlug]['id'],
                        'status' => $status,
                        'started_at' => $index <= $completedCount ? $now->copy()->subDays(7 - $index) : null,
                        'completed_at' => $status === 'completed' ? $now->copy()->subDays(3 - $index) : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $map[$userKey][$topicSlug] = [
                        'id' => $rowId,
                        'status' => $status,
                        'course_slug' => $courseSlug,
                        'course_enrollment_id' => $enrollmentId,
                        'topic_id' => $topics[$topicSlug]['id'],
                    ];
                }
            }
        }

        DB::table('topic_progresses')->insert($rows);

        return $map;
    }

    private function seedMaterialProgresses(array $users, array $materials, array $topicProgresses, Carbon $now): void
    {
        $rows = [];

        foreach ($topicProgresses as $userKey => $topicMap) {
            if (!isset($users[$userKey])) {
                continue;
            }

            foreach ($topicMap as $topicSlug => $progress) {
                $materialList = $materials[$topicSlug] ?? [];

                foreach ($materialList as $index => $material) {
                    $status = 'not_started';

                    if ($progress['status'] === 'completed') {
                        $status = 'completed';
                    } elseif ($index === 0) {
                        $status = 'viewed';
                    }

                    $rows[] = [
                        'id' => $this->uuid(),
                        'user_id' => $users[$userKey],
                        'material_id' => $material['id'],
                        'status' => $status,
                        'started_at' => $status !== 'not_started' ? $now->copy()->subDays(5) : null,
                        'completed_at' => $status === 'completed' ? $now->copy()->subDays(2) : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        DB::table('material_progresses')->insert($rows);
    }

    private function seedUserViews(array $users, array $topics, array $topicProgresses, Carbon $now): void
    {
        $rows = [];

        foreach ($topicProgresses as $userKey => $topicMap) {
            if ($userKey === 'admin') {
                continue;
            }

            foreach ($topicMap as $topicSlug => $progress) {
                if (!in_array($progress['status'], ['completed', 'in_progress'], true)) {
                    continue;
                }

                $rows[] = [
                    'id' => $this->uuid(),
                    'user_id' => $users[$userKey],
                    'topic_id' => $topics[$topicSlug]['id'],
                    'created_at' => $now->copy()->subDays(4),
                    'updated_at' => $now->copy()->subDays(4),
                ];
            }
        }

        DB::table('user_views')->insert($rows);
    }

    private function seedAttendances(array $users, array $sessions, Carbon $now): void
    {
        $rows = [];

        $sessionOrder = [
            'new-birth',
            'spiritual-disciplines',
            'serving-the-church',
            'hermeneutics-basics',
            'sermon-structure',
            'public-speaking-for-ministry',
        ];

        $patterns = [
            'mentor' => ['present', 'present', 'present', 'present', 'present', 'present'],
            'student1' => ['present', 'present', 'present', 'present', 'present', 'present'],
            'student2' => ['present', 'present', 'present', 'present', 'late', 'absent'],
            'student3' => ['present', 'present', 'absent', 'absent', 'absent', 'absent'],
        ];

        foreach ($sessionOrder as $index => $topicSlug) {
            $session = $sessions[$topicSlug] ?? null;
            if (!$session) {
                continue;
            }

            foreach (['mentor', 'student1', 'student2', 'student3'] as $userKey) {
                $status = $patterns[$userKey][$index] ?? 'absent';

                $rows[] = [
                    'id' => $this->uuid(),
                    'video_session_id' => $session['id'],
                    'user_id' => $users[$userKey],
                    'status' => $status,
                    'check_in_at' => in_array($status, ['present', 'late'], true) ? $session['start_at']->copy()->addMinutes($status === 'late' ? 15 : 5) : null,
                    'ip_address' => '127.0.0.' . ($index + 1),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('attendances')->insert($rows);
    }

    private function seedAssessments(array $topics, Carbon $now): array
    {
        $rows = [];
        $map = [];

        foreach ($this->topicBlueprints() as $topic) {
            $id = $this->uuid();

            $rows[] = [
                'id' => $id,
                'topic_id' => $topics[$topic['slug']]['id'],
                'title' => $topic['name'] . ' Post Test',
                'passing_grade' => 70,
                'randomize_questions' => true,
                'question_limit' => 4,
                'status' => 'active',
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now,
            ];

            $map[$topic['slug']] = [
                'id' => $id,
                'topic_id' => $topics[$topic['slug']]['id'],
                'topic_slug' => $topic['slug'],
                'title' => $topic['name'] . ' Post Test',
                'passing_grade' => 70,
            ];
        }

        DB::table('assessments')->insert($rows);

        return $map;
    }

    private function seedQuestionsAndOptions(array $assessments, Carbon $now): array
    {
        $questionMap = [];
        $questionRows = [];
        $optionRows = [];

        foreach ($assessments as $topicSlug => $assessment) {
            for ($i = 1; $i <= 4; $i++) {
                $questionId = $this->uuid();

                $questionRows[] = [
                    'id' => $questionId,
                    'assessment_id' => $assessment['id'],
                    'question' => 'Dummy question ' . $i . ' for ' . $assessment['title'],
                    'explanation' => 'Dummy explanation for question ' . $i,
                    'sort_order' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $optionIds = [];
                $correctOptionId = null;

                $options = [
                    'A' => 'Option A',
                    'B' => 'Option B',
                    'C' => 'Option C',
                    'D' => 'Option D',
                ];

                $sort = 1;
                foreach ($options as $key => $text) {
                    $optionId = $this->uuid();
                    $isCorrect = $key === 'B'; // second option is correct in this dummy dataset

                    $optionRows[] = [
                        'id' => $optionId,
                        'question_id' => $questionId,
                        'option_key' => $key,
                        'option_text' => $text . ' - ' . $assessment['topic_slug'] . ' - Q' . $i,
                        'is_correct' => $isCorrect,
                        'sort_order' => $sort,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $optionIds[] = $optionId;

                    if ($isCorrect) {
                        $correctOptionId = $optionId;
                    }

                    $sort++;
                }

                $questionMap[$assessment['id']]['questions'][] = [
                    'question_id' => $questionId,
                    'correct_option_id' => $correctOptionId,
                    'option_ids' => $optionIds,
                ];
            }
        }

        DB::table('questions')->insert($questionRows);
        DB::table('question_options')->insert($optionRows);

        return $questionMap;
    }

    private function seedAssessmentAttemptsAndAnswers(
        array $users,
        array $assessments,
        array $assessmentQuestions,
        array $topicProgresses,
        Carbon $now
    ): void {
        $attemptRows = [];
        $answerRows = [];

        $usersToSeed = ['mentor', 'student1', 'student2', 'student3'];

        foreach ($usersToSeed as $userKey) {
            foreach ($assessments as $topicSlug => $assessment) {
                $questions = $assessmentQuestions[$assessment['id']]['questions'] ?? [];
                $topicStatus = $topicProgresses[$userKey][$topicSlug]['status'] ?? 'not_started';

                $attemptId = $this->uuid();

                $correctCount = 0;
                $attemptNo = 1;

                foreach ($questions as $index => $questionMeta) {
                    $shouldBeCorrect = false;

                    if ($topicStatus === 'completed') {
                        $shouldBeCorrect = true;
                    } elseif ($topicStatus === 'in_progress' && $index < 2) {
                        $shouldBeCorrect = true;
                    } elseif ($topicStatus === 'not_started' && $index === 0) {
                        $shouldBeCorrect = true;
                    }

                    $selectedOptionId = $questionMeta['correct_option_id'];

                    if (!$shouldBeCorrect) {
                        foreach ($questionMeta['option_ids'] as $optionId) {
                            if ($optionId !== $questionMeta['correct_option_id']) {
                                $selectedOptionId = $optionId;
                                break;
                            }
                        }
                    } else {
                        $correctCount++;
                    }

                    $answerRows[] = [
                        'id' => $this->uuid(),
                        'attempt_id' => $attemptId,
                        'question_id' => $questionMeta['question_id'],
                        'question_option_id' => $selectedOptionId,
                        'answer_text' => null,
                        'is_correct' => $selectedOptionId === $questionMeta['correct_option_id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $score = count($questions) > 0 ? (int) round(($correctCount / count($questions)) * 100) : 0;
                $passed = $score >= ($assessment['passing_grade'] ?? 70);

                $attemptRows[] = [
                    'id' => $attemptId,
                    'assessment_id' => $assessment['id'],
                    'user_id' => $users[$userKey],
                    'attempt_no' => $attemptNo,
                    'score' => $score,
                    'passed' => $passed,
                    'started_at' => $now->copy()->subDays(2),
                    'submitted_at' => $now->copy()->subDays(2)->addMinutes(20),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('assessment_attempts')->insert($attemptRows);
        DB::table('assessment_answers')->insert($answerRows);
    }

    private function seedCertificates(
        array $users,
        array $courses,
        array $topics,
        array $topicProgresses,
        array $enrollments,
        Carbon $now
    ): void {
        $rows = [];
        $topicCounter = 1;
        $courseCounter = 1;

        foreach ($topicProgresses as $userKey => $topicMap) {
            foreach ($topicMap as $topicSlug => $progress) {
                if (($progress['status'] ?? null) !== 'completed') {
                    continue;
                }

                $rows[] = [
                    'id' => $this->uuid(),
                    'certificate_number' => 'CERT-TOPIC-' . str_pad((string) $topicCounter++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $users[$userKey],
                    'type' => 'topic',
                    'course_id' => $courses[$progress['course_slug']] ?? null,
                    'topic_id' => $topics[$topicSlug]['id'] ?? null,
                    'file_path' => 'certificates/topic/' . $userKey . '-' . $topicSlug . '.pdf',
                    'issued_at' => $now->copy()->subDay(),
                    'status' => 'issued',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $topicsByCourse = [
            'foundational-discipleship' => ['new-birth', 'spiritual-disciplines', 'serving-the-church'],
            'sermon-basics' => ['hermeneutics-basics', 'sermon-structure', 'public-speaking-for-ministry'],
        ];

        foreach ($enrollments as $userKey => $courseMap) {
            foreach ($courseMap as $courseSlug => $enrollmentId) {
                $topicSlugs = $topicsByCourse[$courseSlug] ?? [];
                $allCompleted = true;

                foreach ($topicSlugs as $topicSlug) {
                    $status = $topicProgresses[$userKey][$topicSlug]['status'] ?? 'not_started';
                    if ($status !== 'completed') {
                        $allCompleted = false;
                        break;
                    }
                }

                if (!$allCompleted) {
                    continue;
                }

                $rows[] = [
                    'id' => $this->uuid(),
                    'certificate_number' => 'CERT-COURSE-' . str_pad((string) $courseCounter++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $users[$userKey],
                    'type' => 'course',
                    'course_id' => $courses[$courseSlug],
                    'topic_id' => null,
                    'file_path' => 'certificates/course/' . $userKey . '-' . $courseSlug . '.pdf',
                    'issued_at' => $now->copy()->subDay(),
                    'status' => 'issued',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('certificates')->insert($rows);
    }

    private function seedArticles(Carbon $now): void
    {
        $articles = [
            [
                'id' => $this->uuid(),
                'title' => 'Welcome to the LMS',
                'author' => 'System Admin',
                'content' => '<p>Welcome article for dummy content testing.</p>',
                'status' => 'active',
                'clicked' => 12,
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'title' => 'How to Use the Learning Dashboard',
                'author' => 'System Admin',
                'content' => '<p>Step-by-step guide for users.</p>',
                'status' => 'inactive',
                'clicked' => 4,
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now,
            ],
        ];

        DB::table('articles')->insert($articles);

        $articleImages = [
            [
                'id' => $this->uuid(),
                'article_id' => $articles[0]['id'],
                'image' => 'articles/welcome-1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'article_id' => $articles[1]['id'],
                'image' => 'articles/dashboard-1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('article_images')->insert($articleImages);
    }

    private function seedCompany(Carbon $now): void
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        DB::table('companies')->insert([
            'name' => 'Grace Fellowship Church',
            'description' => 'Dummy branding data for LMS testing.',
            'address' => '123 Church Street, City',
            'vision' => 'Growing disciples through structured learning.',
            'mission' => 'Delivering discipleship and sermon learning content.',
            'logo' => 'branding/logo.png',
            'facebook' => 'https://facebook.com/example',
            'instagram' => 'https://instagram.com/example',
            'youtube' => 'https://youtube.com/example',
            'whatsapp' => 'https://wa.me/6281111111111',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedSliders(Carbon $now): void
    {
        $rows = [
            [
                'id' => $this->uuid(),
                'image' => 'sliders/slide-1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'image' => 'sliders/slide-2.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'image' => 'sliders/slide-3.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('sliders')->insert($rows);
    }

    private function seedTutorialVideos(Carbon $now): void
    {
        $rows = [
            [
                'id' => $this->uuid(),
                'video_link' => 'https://www.youtube.com/watch?v=video-setup-1',
                'video_name' => 'Getting Started',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $this->uuid(),
                'video_link' => 'https://www.youtube.com/watch?v=video-setup-2',
                'video_name' => 'How to Join a Session',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('tutorial_videos')->insert($rows);
    }

    private function seedUserDocuments(array $users, Carbon $now): void
    {
        $rows = [];

        foreach ($users as $userKey => $userId) {
            $rows[] = [
                'id' => $this->uuid(),
                'user_id' => $userId,
                'name' => ucfirst($userKey) . ' Profile Photo',
                'image' => 'documents/' . $userKey . '-profile.jpg',
                'type' => 'avatar',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $rows[] = [
                'id' => $this->uuid(),
                'user_id' => $userId,
                'name' => ucfirst($userKey) . ' ID Card',
                'image' => 'documents/' . $userKey . '-id-card.jpg',
                'type' => 'id_card',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('user_documents')->insert($rows);
    
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
