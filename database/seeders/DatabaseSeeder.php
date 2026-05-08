<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();
            $asset = 'images/test.png';

            $this->seedCompany($asset, $now);
            [$permissions, $roles] = $this->seedPermissionsAndRoles($now);
            $this->seedRolePermissions($roles, $permissions);

            $users = $this->seedUsers($roles, $asset, $now);
            $this->seedSocialAccounts($users, $now);

            $programs = $this->seedStudyPrograms($now);
            $courses = $this->seedCourses($programs, $asset, $now);
            $topics = $this->seedTopics($courses, $asset, $now);
            $materials = $this->seedMaterials($topics, $asset, $now);
            $assessments = $this->seedAssessments($courses, $now);
            $questionBanks = $this->seedQuestionsAndOptions($assessments, $topics, $now);

            $enrollments = $this->seedCourseEnrollments($courses, $users, $now);
            $this->seedTopicProgresses($enrollments, $topics, $now);
            $this->seedMaterialProgresses($enrollments, $materials, $topics, $now);
            $attempts = $this->seedAssessmentAttemptsAndAnswers($assessments, $questionBanks, $enrollments, $courses, $now);

            $sessions = $this->seedVideoSessions($topics, $now);
            $this->seedAttendances($sessions, $enrollments, $now);
            $this->seedCertificates($enrollments, $courses, $topics, $now);
            $this->seedArticles($users, $asset, $now);
            $this->seedTutorialVideos($now);
            $this->seedActivityLogs($users, $courses, $topics, $enrollments, $attempts, $sessions, $now);
        });
    }

    private function uuid(): string
    {
        return (string) Str::uuid();
    }

    private function courseSlugMap(array $courses): array
    {
        $map = [];
        foreach ($courses as $slug => $course) {
            $map[$course['id']] = $slug;
        }
        return $map;
    }

    private function seedCompany(string $asset, $now): void
    {
        DB::table('companies')->insert([
            [
                'id' => $this->uuid(),
                'name' => 'PT Edukasi Digital Nusantara',
                'description' => 'Platform pembelajaran organisasi pemuridan untuk content delivery, session scheduling, assessment, dan certification.',
                'address' => 'Jl. Jenderal Sudirman No. 88, Jakarta Selatan, DKI Jakarta',
                'vision' => 'Menjadi ekosistem pembelajaran digital yang relevan, modern, dan terukur untuk pelayanan dan pembinaan.',
                'mission' => 'Menyediakan pengalaman belajar yang terstruktur, interaktif, dan dapat dipertanggungjawabkan untuk komunitas pembelajaran.',
                'logo' => $asset,
                'facebook' => 'https://facebook.com/edunusa',
                'instagram' => 'https://instagram.com/edunusa',
                'youtube' => 'https://youtube.com/@edunusa',
                'whatsapp' => '+62-812-0000-9001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedPermissionsAndRoles($now): array
    {
        $permissions = ['manage_users', 'manage_courses', 'manage_topics', 'access_topic', 'enroll_course', 'take_assessment', 'manage_assessments', 'manage_certificates', 'view_reports'];
        $permissionRows = [];
        foreach ($permissions as $name) {
            $permissionRows[$name] = ['id' => $this->uuid(), 'name' => $name, 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('permissions')->insert(array_values($permissionRows));

        $roles = [
            'admin' => ['id' => $this->uuid(), 'name' => 'admin', 'label' => 'Admin', 'description' => 'Full control atas course, topic, assessment, certificate, dan report.', 'created_at' => $now, 'updated_at' => $now],
            'student' => ['id' => $this->uuid(), 'name' => 'student', 'label' => 'Student', 'description' => 'Peserta pembelajaran yang mengakses materi, sesi, dan assessment.', 'created_at' => $now, 'updated_at' => $now],
            'disciples' => ['id' => $this->uuid(), 'name' => 'disciples', 'label' => 'Disciples', 'description' => 'Mentor/tutor yang juga dapat berperan sebagai student dan mengelola topik.', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('roles')->insert(array_values($roles));

        return [$permissionRows, $roles];
    }

    private function seedRolePermissions(array $roles, array $permissions): void
    {
        $map = [
            'admin' => ['manage_users', 'manage_courses', 'manage_topics', 'manage_assessments', 'manage_certificates', 'view_reports'],
            'student' => ['enroll_course', 'access_topic', 'take_assessment'],
            'disciples' => ['access_topic', 'manage_topics', 'manage_assessments', 'view_reports'],
        ];

        $rows = [];
        foreach ($map as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                $rows[] = ['role_id' => $roles[$roleName]['id'], 'permission_id' => $permissions[$permissionName]['id']];
            }
        }
        DB::table('role_permission')->insert($rows);
    }

    private function seedUsers(array $roles, string $asset, $now): array
    {
        $profiles = [
            ['name' => 'System Administrator', 'email' => 'admin@example.test', 'gender' => 'male', 'phone' => '+62-811-0000-0001', 'dob' => '1990-01-01', 'roles' => ['admin']],
            ['name' => 'Testing Disciple', 'email' => 'disciple@example.test', 'gender' => 'male', 'phone' => '+62-811-0000-0002', 'dob' => '1992-02-02', 'roles' => ['disciples', 'student']],
            ['name' => 'Testing Student', 'email' => 'student@example.test', 'gender' => 'female', 'phone' => '+62-811-0000-0003', 'dob' => '2001-03-03', 'roles' => ['student']],
            ['name' => 'Alya Nabila', 'email' => 'alya.nabila@edunusa.test', 'gender' => 'female', 'phone' => '+62-814-5000-4001', 'dob' => '2002-05-12', 'roles' => ['student']],
            ['name' => 'Rizal Fadlan', 'email' => 'rizal.fadlan@edunusa.test', 'gender' => 'male', 'phone' => '+62-814-5000-4002', 'dob' => '2001-07-08', 'roles' => ['student']],
            ['name' => 'Sinta Rahma', 'email' => 'sinta.rahma@edunusa.test', 'gender' => 'female', 'phone' => '+62-814-5000-4003', 'dob' => '2002-09-21', 'roles' => ['student']],
            ['name' => 'Dewa Pratama', 'email' => 'dewa.pratama@edunusa.test', 'gender' => 'male', 'phone' => '+62-814-5000-4004', 'dob' => '2000-12-18', 'roles' => ['student']],
            ['name' => 'Putri Azzahra', 'email' => 'putri.azzahra@edunusa.test', 'gender' => 'female', 'phone' => '+62-814-5000-4005', 'dob' => '2002-03-15', 'roles' => ['student']],
            ['name' => 'Rafi Hidayat', 'email' => 'rafi.hidayat@edunusa.test', 'gender' => 'male', 'phone' => '+62-814-5000-4006', 'dob' => '2001-11-27', 'roles' => ['student']],
            ['name' => 'Salma Khairunnisa', 'email' => 'salma.khairunnisa@edunusa.test', 'gender' => 'female', 'phone' => '+62-814-5000-4007', 'dob' => '2002-06-02', 'roles' => ['student']],
        ];

        $rows = [];
        $byEmail = [];
        $byRole = ['admin' => [], 'student' => [], 'disciples' => []];

        foreach ($profiles as $profile) {
            $row = [
                'id' => $this->uuid(),
                'name' => $profile['name'],
                'email' => $profile['email'],
                'email_verified_at' => $now->copy()->subDays(rand(1, 30)),
                'password' => Hash::make('Password123!'),
                'phone' => $profile['phone'],
                'gender' => $profile['gender'],
                'dob' => $profile['dob'],
                'image' => $asset,
                'remember_token' => Str::random(10),
                'created_at' => $now->copy()->subDays(rand(10, 180)),
                'updated_at' => $now,
            ];
            $rows[] = $row;
            $byEmail[$profile['email']] = $row;
            foreach ($profile['roles'] as $roleName) {
                $byRole[$roleName][] = $row;
            }
        }
        DB::table('users')->insert($rows);

        $pivotRows = [];
        foreach ($profiles as $profile) {
            $user = $byEmail[$profile['email']];
            foreach ($profile['roles'] as $roleName) {
                $pivotRows[] = ['user_id' => $user['id'], 'role_id' => $roles[$roleName]['id'], 'assigned_at' => $now->copy()->subDays(rand(3, 75)), 'created_at' => $now, 'updated_at' => $now];
            }
        }
        DB::table('role_user')->insert($pivotRows);

        return ['all' => $rows, 'byEmail' => $byEmail, 'byRole' => $byRole];
    }

    private function seedSocialAccounts(array $users, $now): void
    {
        DB::table('social_accounts')->insert([
            ['id' => $this->uuid(), 'user_id' => $users['byEmail']['admin@example.test']['id'], 'provider' => 'google', 'provider_user_id' => 'google-admin-001', 'provider_email' => 'admin@example.test', 'token' => Str::random(64), 'refresh_token' => Str::random(64), 'expires_at' => $now->copy()->addMonths(6), 'created_at' => $now, 'updated_at' => $now],
            ['id' => $this->uuid(), 'user_id' => $users['byEmail']['disciple@example.test']['id'], 'provider' => 'google', 'provider_user_id' => 'google-disciple-001', 'provider_email' => 'disciple@example.test', 'token' => Str::random(64), 'refresh_token' => Str::random(64), 'expires_at' => $now->copy()->addMonths(4), 'created_at' => $now, 'updated_at' => $now],
            ['id' => $this->uuid(), 'user_id' => $users['byEmail']['student@example.test']['id'], 'provider' => 'google', 'provider_user_id' => 'google-student-001', 'provider_email' => 'student@example.test', 'token' => Str::random(64), 'refresh_token' => Str::random(64), 'expires_at' => $now->copy()->addMonths(3), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedStudyPrograms($now): array
    {
        $rows = [
            'discipleship' => ['id' => $this->uuid(), 'title' => 'Discipleship', 'slug' => 'discipleship', 'description' => 'Program pembinaan dan pemuridan untuk pertumbuhan iman dan komunitas.', 'status' => 'active', 'created_at' => $now->copy()->subMonths(8), 'updated_at' => $now],
            'sermon' => ['id' => $this->uuid(), 'title' => 'Sermon', 'slug' => 'sermon', 'description' => 'Program pembelajaran penyampaian firman, exegesis, dan komunikasi publik.', 'status' => 'active', 'created_at' => $now->copy()->subMonths(7), 'updated_at' => $now],
            'legacy-ministry-training' => ['id' => $this->uuid(), 'title' => 'Legacy Ministry Training', 'slug' => 'legacy-ministry-training', 'description' => 'Program lama yang sudah tidak aktif namun tetap tersimpan untuk referensi.', 'status' => 'inactive', 'created_at' => $now->copy()->subYear(), 'updated_at' => $now],
        ];
        DB::table('study_programs')->insert(array_values($rows));
        return $rows;
    }

    private function seedCourses(array $programs, string $asset, $now): array
    {
        $rows = [
            'discipleship-foundations' => ['id' => $this->uuid(), 'study_program_id' => $programs['discipleship']['id'], 'title' => 'Discipleship Foundations', 'slug' => 'discipleship-foundations', 'poster' => $asset, 'credit' => 3, 'description' => 'Jalur dasar pemuridan untuk peserta baru.', 'quota' => 30, 'status' => 'active', 'created_at' => $now->copy()->subMonths(6), 'updated_at' => $now, '_phase' => 'completed', '_teacher_email' => 'maya.sari@edunusa.test', '_program_slug' => 'discipleship'],
            'discipleship-growth-track' => ['id' => $this->uuid(), 'study_program_id' => $programs['discipleship']['id'], 'title' => 'Discipleship Growth Track', 'slug' => 'discipleship-growth-track', 'poster' => $asset, 'credit' => 3, 'description' => 'Jalur pengembangan pemuridan yang sedang berjalan dengan 4 dari 8 topik selesai.', 'quota' => 30, 'status' => 'active', 'created_at' => $now->copy()->subMonths(5), 'updated_at' => $now, '_phase' => 'in_progress', '_teacher_email' => 'fajar.hidayat@edunusa.test', '_program_slug' => 'discipleship'],
            'sermon-foundations' => ['id' => $this->uuid(), 'study_program_id' => $programs['sermon']['id'], 'title' => 'Sermon Foundations', 'slug' => 'sermon-foundations', 'poster' => $asset, 'credit' => 3, 'description' => 'Jalur dasar persiapan sermon dan interpretasi teks.', 'quota' => 24, 'status' => 'active', 'created_at' => $now->copy()->subMonths(5), 'updated_at' => $now, '_phase' => 'completed', '_teacher_email' => 'bagas.wiratama@edunusa.test', '_program_slug' => 'sermon'],
            'sermon-outreach-lab' => ['id' => $this->uuid(), 'study_program_id' => $programs['sermon']['id'], 'title' => 'Sermon Outreach Lab', 'slug' => 'sermon-outreach-lab', 'poster' => $asset, 'credit' => 2, 'description' => 'Jalur internal yang belum dibuka untuk peserta; topik masih draft.', 'quota' => 20, 'status' => 'inactive', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now, '_phase' => 'pre_learning', '_teacher_email' => 'nadia.prameswari@edunusa.test', '_program_slug' => 'sermon'],
        ];

        DB::table('courses')->insert(array_map(fn ($row) => collect($row)->except(['_phase','_teacher_email','_program_slug'])->all(), $rows));
        return $rows;
    }

    private function topicBlueprints(): array
    {
        return [
            'discipleship-foundations' => ['Call to Follow', 'Bible Reading Rhythm', 'Prayer and Reflection', 'Community and Fellowship', 'Serving with Integrity', 'Accountability and Mentoring', 'Stewardship and Discipline', 'Final Reflection Project'],
            'discipleship-growth-track' => ['Core Doctrine Review', 'Daily Habits and Time Stewardship', 'Conflict Handling in Community', 'Mentoring Conversation Practice', 'Evangelism Basics', 'Leading Small Group Discussion', 'Personal Growth Journal', 'Workshop Wrap-up'],
            'sermon-foundations' => ['Text Observation', 'Historical and Literary Context', 'Exegesis Essentials', 'Big Idea Development', 'Sermon Outline Structure', 'Illustration Selection', 'Delivery and Voice', 'Feedback and Revision'],
            'sermon-outreach-lab' => ['Worship Context', 'Outreach Messaging', 'Small Group Facilitation', 'Follow-up Discipline', 'Media and Visual Support', 'Schedule and Planning', 'Safety and Pastoral Care', 'Program Wrap-up'],
        ];
    }

    private function seedTopics(array $courses, string $asset, $now): array
    {
        $rows = [];
        foreach ($courses as $courseSlug => $course) {
            $topicNames = $this->topicBlueprints()[$courseSlug];
            foreach ($topicNames as $index => $topicName) {
                $status = 'draft';
                if ($course['_phase'] === 'completed') {
                    $status = 'published';
                } elseif ($course['_phase'] === 'in_progress') {
                    $status = $index < 4 ? 'published' : 'draft';
                } else {
                    $status = $index === 7 ? 'archived' : 'draft';
                }

                $rows[$courseSlug][] = [
                    'id' => $this->uuid(),
                    'course_id' => $course['id'],
                    'teacher_id' => $this->teacherIdForCourse($course['_teacher_email']),
                    'name' => $topicName,
                    'slug' => Str::slug($courseSlug . '-' . $topicName),
                    'category' => $course['_program_slug'],
                    'description' => $topicName . ' untuk course ' . $course['title'] . '.',
                    'poster' => $asset,
                    'visibility' => 'public',
                    'status' => $status,
                    'sort_order' => $index + 1,
                    'created_at' => $now->copy()->subWeeks(rand(2, 16)),
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('topics')->insert(array_merge(...array_values($rows)));
        return $rows;
    }

    private function teacherIdForCourse(string $teacherEmail): string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = DB::table('users')->pluck('id', 'email')->all();
        }
        return $cache['disciple@example.test']
            ?? $cache[$teacherEmail]
            ?? array_values($cache)[0];
    }

    private function seedMaterials(array $topicsByCourse, string $asset, $now): array
    {
        $rows = [];
        foreach ($topicsByCourse as $courseSlug => $topics) {
            foreach ($topics as $topic) {
                foreach (['video', 'pdf', 'ppt'] as $index => $type) {
                    $rows[] = [
                        'id' => $this->uuid(),
                        'topic_id' => $topic['id'],
                        'uploader_id' => $topic['teacher_id'],
                        'name' => $topic['name'] . ' ' . strtoupper($type),
                        'type' => $type,
                        'path' => $type === 'video' ? null : 'materials/' . Str::slug($courseSlug . '-' . $topic['name'] . '-' . $type) . '.' . ($type === 'ppt' ? 'ppt' : $type),
                        'external_url' => $type === 'video' ? 'https://youtu.be/' . Str::lower(Str::random(11)) : null,
                        'visibility' => 'public',
                        'sort_order' => $index + 1,
                        'status' => $topic['status'] === 'published' ? 'active' : 'inactive',
                        'created_at' => $now->copy()->subWeeks(rand(1, 12)),
                        'updated_at' => $now,
                    ];
                }
            }
        }
        DB::table('materials')->insert($rows);
        return $rows;
    }

    private function seedAssessments(array $courses, $now): array
    {
        $rows = [
            'discipleship-foundations' => ['id' => $this->uuid(), 'course_id' => $courses['discipleship-foundations']['id'], 'title' => 'Discipleship Foundations Assessment', 'passing_grade' => 70, 'randomize_questions' => 1, 'question_limit' => 8, 'time_limit_minutes' => 30, 'status' => 'active', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now],
            'discipleship-growth-track' => ['id' => $this->uuid(), 'course_id' => $courses['discipleship-growth-track']['id'], 'title' => 'Discipleship Growth Track Assessment', 'passing_grade' => 70, 'randomize_questions' => 1, 'question_limit' => 8, 'time_limit_minutes' => 30, 'status' => 'draft', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now],
            'sermon-foundations' => ['id' => $this->uuid(), 'course_id' => $courses['sermon-foundations']['id'], 'title' => 'Sermon Foundations Assessment', 'passing_grade' => 70, 'randomize_questions' => 1, 'question_limit' => 8, 'time_limit_minutes' => 30, 'status' => 'active', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now],
            'sermon-outreach-lab' => ['id' => $this->uuid(), 'course_id' => $courses['sermon-outreach-lab']['id'], 'title' => 'Sermon Outreach Lab Assessment', 'passing_grade' => 70, 'randomize_questions' => 0, 'question_limit' => 8, 'time_limit_minutes' => 20, 'status' => 'inactive', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now],
        ];
        DB::table('assessments')->insert(array_values($rows));
        return $rows;
    }

    private function seedQuestionsAndOptions(array $assessments, array $topics, $now): array
    {
        $questionBanks = [];
        $questionRows = [];
        $optionRows = [];

        foreach (['discipleship-foundations', 'discipleship-growth-track', 'sermon-foundations'] as $courseSlug) {
            $assessment = $assessments[$courseSlug];
            $topicNames = array_map(fn ($t) => $t['name'], $topics[$courseSlug]);

            foreach ($topicNames as $sortOrder => $topicName) {
                $questionId = $this->uuid();
                $questionRows[] = [
                    'id' => $questionId,
                    'assessment_id' => $assessment['id'],
                    'question_type' => 'mcq',
                    'question' => 'Which practice best reflects ' . $topicName . '?',
                    'correct_text_answer' => null,
                    'explanation' => 'Aligned with the learning objective of the topic.',
                    'sort_order' => $sortOrder + 1,
                    'created_at' => $now->copy()->subDays(rand(5, 45)),
                    'updated_at' => $now,
                ];

                $correct = $this->uuid();
                $wrong1 = $this->uuid();
                $wrong2 = $this->uuid();
                $wrong3 = $this->uuid();

                $options = [
                    ['id' => $correct, 'text' => 'Applying the core lesson of ' . $topicName, 'is_correct' => true],
                    ['id' => $wrong1, 'text' => 'Ignoring the main learning point', 'is_correct' => false],
                    ['id' => $wrong2, 'text' => 'Replacing the topic with unrelated content', 'is_correct' => false],
                    ['id' => $wrong3, 'text' => 'Submitting without reading the material', 'is_correct' => false],
                ];

                foreach ($options as $index => $option) {
                    $optionRows[] = [
                        'id' => $option['id'],
                        'question_id' => $questionId,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'],
                        'sort_order' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $questionBanks[$courseSlug][] = [
                    'id' => $questionId,
                    'correct_option_id' => $correct,
                    'wrong_option_ids' => [$wrong1, $wrong2, $wrong3],
                ];
            }
        }

        DB::table('questions')->insert($questionRows);
        DB::table('question_options')->insert($optionRows);

        return $questionBanks;
    }

    private function seedCourseEnrollments(array $courses, array $users, $now): array
    {
        $plan = [
            'discipleship-foundations' => [
                ['email' => 'student@example.test', 'status' => 'completed'],
                ['email' => 'alya.nabila@edunusa.test', 'status' => 'completed'],
                ['email' => 'rizal.fadlan@edunusa.test', 'status' => 'completed'],
            ],
            'discipleship-growth-track' => [
                ['email' => 'disciple@example.test', 'status' => 'active'],
                ['email' => 'citra.lestari@edunusa.test', 'status' => 'active'],
                ['email' => 'fajar.hidayat@edunusa.test', 'status' => 'active'],
            ],
            'sermon-foundations' => [
                ['email' => 'sinta.rahma@edunusa.test', 'status' => 'completed'],
                ['email' => 'dewa.pratama@edunusa.test', 'status' => 'completed'],
                ['email' => 'putri.azzahra@edunusa.test', 'status' => 'completed'],
            ],
            'sermon-outreach-lab' => [],
        ];

        $rows = [];
        $byCourse = ['discipleship-foundations' => [], 'discipleship-growth-track' => [], 'sermon-foundations' => [], 'sermon-outreach-lab' => []];
        $completed = [];
        $seenCourseUser = [];

        foreach ($plan as $courseSlug => $items) {
            foreach ($items as $index => $item) {
                $user = $users['byEmail'][$item['email']] ?? null;
                if (! $user) {
                    continue;
                }

                $pairKey = $courseSlug . '|' . $user['id'];
                if (isset($seenCourseUser[$pairKey])) {
                    continue;
                }
                $seenCourseUser[$pairKey] = true;

                $enrolledAt = $now->copy()->subDays(50 + ($index * 5));
                $completedAt = $item['status'] === 'completed' ? $enrolledAt->copy()->addDays(18 + $index) : null;

                $row = [
                    'id' => $this->uuid(),
                    'user_id' => $user['id'],
                    'course_id' => $courses[$courseSlug]['id'],
                    'status' => $item['status'],
                    'enrolled_at' => $enrolledAt,
                    'completed_at' => $completedAt,
                    'created_at' => $enrolledAt,
                    'updated_at' => $completedAt ?? $now,
                ];

                $rows[] = $row;
                $byCourse[$courseSlug][] = $row;
                if ($item['status'] === 'completed') {
                    $completed[] = $row;
                }
            }
        }

        if (! empty($rows)) {
            DB::table('course_enrollments')->insert($rows);
        }

        return ['all' => $rows, 'byCourse' => $byCourse, 'completed' => $completed];
    }

    private function seedTopicProgresses(array $enrollments, array $topics, $now): void
    {
        $rows = [];
        foreach ($enrollments['byCourse'] as $courseSlug => $courseEnrollments) {
            foreach ($courseEnrollments as $enrollment) {
                foreach ($topics[$courseSlug] as $index => $topic) {
                    if ($enrollment['status'] === 'completed') {
                        $status = 'completed';
                        $startedAt = $enrollment['enrolled_at']->copy()->addDays($index + 1);
                        $completedAt = $startedAt->copy()->addDay();
                    } elseif ($courseSlug === 'discipleship-growth-track') {
                        if ($index < 4) {
                            $status = 'completed';
                            $startedAt = $enrollment['enrolled_at']->copy()->addDays($index + 1);
                            $completedAt = $startedAt->copy()->addDay();
                        } else {
                            $status = 'started';
                            $startedAt = $now->copy()->subDays(rand(2, 15));
                            $completedAt = null;
                        }
                    } else {
                        $status = 'pending';
                        $startedAt = null;
                        $completedAt = null;
                    }

                    $rows[] = [
                        'id' => $this->uuid(),
                        'course_enrollment_id' => $enrollment['id'],
                        'topic_id' => $topic['id'],
                        'status' => $status,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'created_at' => $startedAt ?? $enrollment['enrolled_at'],
                        'updated_at' => $completedAt ?? $now,
                    ];
                }
            }
        }
        DB::table('topic_progresses')->insert($rows);
    }

    private function seedMaterialProgresses(array $enrollments, array $materials, array $topics, $now): void
    {
        $rows = [];
        $materialsByTopic = [];
        foreach ($materials as $material) {
            $materialsByTopic[$material['topic_id']][] = $material;
        }

        foreach ($enrollments['byCourse'] as $courseSlug => $courseEnrollments) {
            foreach ($courseEnrollments as $enrollment) {
                foreach ($topics[$courseSlug] as $index => $topic) {
                    foreach (($materialsByTopic[$topic['id']] ?? []) as $material) {
                        if ($material['status'] !== 'active') {
                            continue;
                        }

                        $completed = $enrollment['status'] === 'completed' || ($courseSlug === 'discipleship-growth-track' && $index < 4);
                        $startedAt = $enrollment['enrolled_at']->copy()->addDays($index + 1);
                        $completedAt = $completed ? $startedAt->copy()->addHours(4) : null;

                        $rows[] = [
                            'id' => $this->uuid(),
                            'user_id' => $enrollment['user_id'],
                            'material_id' => $material['id'],
                            'status' => $completed ? 'completed' : 'started',
                            'started_at' => $startedAt,
                            'completed_at' => $completedAt,
                            'created_at' => $startedAt,
                            'updated_at' => $completedAt ?? $now,
                        ];
                    }
                }
            }
        }

        DB::table('material_progresses')->insert($rows);
    }

    private function seedAssessmentAttemptsAndAnswers(array $assessments, array $questionBanks, array $enrollments, array $courses, $now): array
    {
        $courseSlugMap = $this->courseSlugMap($courses);
        $attemptRows = [];
        $answerRows = [];
        $attempts = [];

        foreach ($enrollments['completed'] as $enrollment) {
            $courseSlug = $courseSlugMap[$enrollment['course_id']] ?? null;
            if (!$courseSlug || !isset($questionBanks[$courseSlug]) || empty($questionBanks[$courseSlug])) {
                continue;
            }

            $assessment = $assessments[$courseSlug];
            if ($assessment['status'] !== 'active') {
                continue;
            }

            $attemptId = $this->uuid();
            $startedAt = $enrollment['completed_at']->copy()->addHour();
            $submittedAt = $startedAt->copy()->addMinutes(rand(10, 25));
            $score = rand(78, 98);

            $attempt = [
                'id' => $attemptId,
                'assessment_id' => $assessment['id'],
                'user_id' => $enrollment['user_id'],
                'attempt_no' => 1,
                'score' => $score,
                'duration_seconds' => rand(900, 1800),
                'passed' => $score >= $assessment['passing_grade'],
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
                'created_at' => $startedAt,
                'updated_at' => $submittedAt,
            ];

            $attemptRows[] = $attempt;
            $attempts[] = $attempt;

            foreach ($questionBanks[$courseSlug] as $question) {
                $answerRows[] = [
                    'id' => $this->uuid(),
                    'attempt_id' => $attemptId,
                    'question_id' => $question['id'],
                    'question_option_id' => $question['correct_option_id'],
                    'answer_text' => null,
                    'is_correct' => true,
                    'created_at' => $submittedAt,
                    'updated_at' => $submittedAt,
                ];
            }
        }

        DB::table('assessment_attempts')->insert($attemptRows);
        DB::table('assessment_answers')->insert($answerRows);

        return $attempts;
    }

    private function seedVideoSessions(array $topics, $now): array
    {
        $plans = [
            'discipleship-foundations' => ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed'],
            'discipleship-growth-track' => ['completed', 'completed', 'completed', 'completed', 'ongoing', 'scheduled', 'scheduled', 'cancelled'],
            'sermon-foundations' => ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed'],
            'sermon-outreach-lab' => ['scheduled', 'cancelled', 'scheduled'],
        ];

        $rows = [];
        $sessions = [];

        foreach ($plans as $courseSlug => $statuses) {
            foreach ($statuses as $index => $status) {
                $topic = $topics[$courseSlug][$index];
                $startAt = $now->copy()->addDays($courseSlug === 'discipleship-growth-track' ? -20 : -30)->addDays($index * 2)->setTime(19, 0);
                if ($status === 'ongoing') {
                    $startAt = $now->copy()->subMinutes(20);
                    $endAt = $now->copy()->addMinutes(40);
                } elseif ($status === 'scheduled') {
                    $startAt = $now->copy()->addDays(2 + $index);
                    $endAt = $startAt->copy()->addHours(2);
                } elseif ($status === 'cancelled') {
                    $startAt = $now->copy()->addDays(5 + $index);
                    $endAt = $startAt->copy()->addHours(2);
                } else {
                    $endAt = $startAt->copy()->addHours(2);
                }

                $row = [
                    'id' => $this->uuid(),
                    'topic_id' => $topic['id'],
                    'title' => $topic['name'] . ' Live Session',
                    'zoom_link' => 'https://zoom.example.test/' . Str::slug($courseSlug . '-' . $topic['name']) . '-' . Str::random(6),
                    'record_link' => in_array($status, ['completed', 'ongoing'], true) ? 'https://recording.example.test/' . Str::slug($courseSlug . '-' . $topic['name']) : null,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'reminder_sent_at' => in_array($status, ['completed', 'ongoing'], true) ? $startAt->copy()->subHours(2) : null,
                    'status' => $status,
                    'created_at' => $now->copy()->subWeeks(rand(2, 8)),
                    'updated_at' => $now,
                    '_course_slug' => $courseSlug,
                ];

                $rows[] = collect($row)->except(['_course_slug'])->all();
                $sessions[] = $row;
            }
        }

        DB::table('video_sessions')->insert($rows);
        return $sessions;
    }

    private function seedAttendances(array $sessions, array $enrollments, $now): void
    {
        $rows = [];
        foreach ($sessions as $session) {
            if (!in_array($session['status'], ['completed', 'ongoing'], true)) {
                continue;
            }

            $courseEnrollments = $enrollments['byCourse'][$session['_course_slug']] ?? [];
            foreach (array_slice($courseEnrollments, 0, 3) as $index => $enrollment) {
                $status = match ($index) {
                    0 => 'present',
                    1 => 'late',
                    default => 'absent',
                };

                $rows[] = [
                    'id' => $this->uuid(),
                    'video_session_id' => $session['id'],
                    'user_id' => $enrollment['user_id'],
                    'status' => $status,
                    'check_in_at' => in_array($status, ['present', 'late'], true) ? $session['start_at']->copy()->addMinutes(5 + ($index * 3)) : null,
                    'clock_out_at' => $status === 'present' ? $session['end_at']->copy()->subMinutes(10) : null,
                    'ip_address' => '10.10.0.' . rand(10, 250),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('attendances')->insert($rows);
    }

    private function seedCertificates(array $enrollments, array $courses, array $topics, $now): void
    {
        $courseSlugMap = $this->courseSlugMap($courses);
        $rows = [];

        foreach ($enrollments['completed'] as $enrollment) {
            $courseSlug = $courseSlugMap[$enrollment['course_id']] ?? null;
            if (!$courseSlug) {
                continue;
            }

            foreach ($topics[$courseSlug] as $index => $topic) {
                $rows[] = [
                    'id' => $this->uuid(),
                    'certificate_number' => 'TPC-' . strtoupper(Str::slug($courseSlug)) . '-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'user_id' => $enrollment['user_id'],
                    'type' => 'topic',
                    'course_id' => $enrollment['course_id'],
                    'topic_id' => $topic['id'],
                    'file_path' => 'certificates/topics/' . Str::slug($courseSlug) . '/' . Str::uuid() . '.pdf',
                    'issued_at' => $enrollment['completed_at']->copy()->addDays($index + 1),
                    'status' => 'issued',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $rows[] = [
                'id' => $this->uuid(),
                'certificate_number' => 'CRC-' . strtoupper(Str::slug($courseSlug)) . '-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => $enrollment['user_id'],
                'type' => 'course',
                'course_id' => $enrollment['course_id'],
                'topic_id' => null,
                'file_path' => 'certificates/courses/' . Str::slug($courseSlug) . '/' . Str::uuid() . '.pdf',
                'issued_at' => $enrollment['completed_at']->copy()->addWeek(),
                'status' => 'issued',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $draftEnrollment = $enrollments['byCourse']['discipleship-growth-track'][0] ?? null;
        if ($draftEnrollment) {
            $rows[] = [
                'id' => $this->uuid(),
                'certificate_number' => 'DRAFT-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => $draftEnrollment['user_id'],
                'type' => 'course',
                'course_id' => $draftEnrollment['course_id'],
                'topic_id' => null,
                'file_path' => 'certificates/drafts/' . Str::uuid() . '.pdf',
                'issued_at' => null,
                'status' => 'draft',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now,
            ];
        }

        $expiredEnrollment = $enrollments['completed'][0] ?? null;
        if ($expiredEnrollment) {
            $rows[] = [
                'id' => $this->uuid(),
                'certificate_number' => 'EXP-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => $expiredEnrollment['user_id'],
                'type' => 'course',
                'course_id' => $expiredEnrollment['course_id'],
                'topic_id' => null,
                'file_path' => 'certificates/expired/' . Str::uuid() . '.pdf',
                'issued_at' => $now->copy()->subYears(2),
                'status' => 'expired',
                'created_at' => $now->copy()->subYears(2),
                'updated_at' => $now,
            ];
        }

        DB::table('certificates')->insert($rows);
    }

    private function seedArticles(array $users, string $asset, $now): void
    {
        $author = $users['byEmail']['admin@example.test']['name'];
        DB::table('articles')->insert([
            ['id' => $this->uuid(), 'title' => 'Cara Menyusun Discipleship Path yang Terstruktur', 'image' => $asset, 'author' => $author, 'content' => 'Panduan pengelolaan jalur pemuridan yang terukur, terjadwal, dan mudah dipantau oleh admin maupun mentor.', 'status' => 'active', 'clicked' => 52, 'created_at' => $now->copy()->subDays(14), 'updated_at' => $now],
            ['id' => $this->uuid(), 'title' => 'Draft: Rencana Konten Sermon Bulanan', 'image' => $asset, 'author' => $author, 'content' => 'Draft internal untuk pengelolaan konten sermon pada bulan berikutnya.', 'status' => 'draft', 'clicked' => 6, 'created_at' => $now->copy()->subDays(7), 'updated_at' => $now],
            ['id' => $this->uuid(), 'title' => 'Legacy Article Archive', 'image' => $asset, 'author' => $author, 'content' => 'Artikel lama yang dinonaktifkan dari tampilan publik namun tetap disimpan untuk referensi.', 'status' => 'inactive', 'clicked' => 11, 'created_at' => $now->copy()->subMonths(3), 'updated_at' => $now],
        ]);
    }

    private function seedTutorialVideos($now): void
    {
        DB::table('tutorial_videos')->insert([
            ['id' => $this->uuid(), 'video_link' => 'https://youtu.be/example-discipleship', 'video_name' => 'Intro to Discipleship Learning', 'created_at' => $now->copy()->subMonths(3), 'updated_at' => $now],
            ['id' => $this->uuid(), 'video_link' => 'https://youtu.be/example-sermon', 'video_name' => 'Sermon Preparation Overview', 'created_at' => $now->copy()->subMonths(2), 'updated_at' => $now],
        ]);
    }

    private function seedActivityLogs(array $users, array $courses, array $topics, array $enrollments, array $attempts, array $sessions, $now): void
    {
        $courseSlugMap = $this->courseSlugMap($courses);
        $rows = [];

        $firstCompleted = $enrollments['completed'][0] ?? null;
        $firstAttempt = $attempts[0] ?? null;
        $firstSession = $sessions[0] ?? null;
        $firstCourseSlug = $firstCompleted ? ($courseSlugMap[$firstCompleted['course_id']] ?? null) : null;
        $firstTopic = $firstCourseSlug ? ($topics[$firstCourseSlug][0] ?? null) : null;

        $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['student@example.test']['id'], 'action' => 'course.enrolled', 'payload' => json_encode(['course_id' => $firstCompleted['course_id'] ?? null, 'course_title' => 'Discipleship Foundations', 'source' => 'student_portal'], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(40), 'updated_at' => $now];
        if ($firstTopic) $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['disciple@example.test']['id'], 'action' => 'topic.completed', 'payload' => json_encode(['topic_id' => $firstTopic['id'], 'topic_name' => $firstTopic['name']], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(22), 'updated_at' => $now];
        if ($firstAttempt) $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['student@example.test']['id'], 'action' => 'assessment.submitted', 'payload' => json_encode(['attempt_id' => $firstAttempt['id'], 'score' => $firstAttempt['score']], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(18), 'updated_at' => $now];
        if ($firstCompleted) $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['admin@example.test']['id'], 'action' => 'certificate.issued', 'payload' => json_encode(['certificate_type' => 'course', 'course_id' => $firstCompleted['course_id'], 'user_id' => $firstCompleted['user_id']], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(10), 'updated_at' => $now];
        if ($firstSession) $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['admin@example.test']['id'], 'action' => 'attendance.issue', 'payload' => json_encode(['video_session_id' => $firstSession['id'], 'issue' => 'Late check-in and incomplete attendance capture'], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(7), 'updated_at' => $now];
        if ($firstSession) $rows[] = ['id' => $this->uuid(), 'user_id' => $users['byEmail']['disciple@example.test']['id'], 'action' => 'session.reminder.sent', 'payload' => json_encode(['video_session_id' => $firstSession['id'], 'status' => 'delivered'], JSON_UNESCAPED_UNICODE), 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now];

        DB::table('activity_logs')->insert($rows);
    }
}
