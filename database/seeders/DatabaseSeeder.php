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
            $logoAsset = 'images/logo.png';
            $courseAsset = 'images/thumbnail/thumbnail_dove.png';

            $this->seedCompany($logoAsset, $now);
            [$permissions, $roles] = $this->seedPermissionsAndRoles($now);
            $this->seedRolePermissions($roles, $permissions);

            $users = $this->seedUsers($roles, $logoAsset, $now);
            $this->seedAdditionalLearningData($users, $courseAsset, $now);
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
                'name' => 'Miracle Institute',
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
        $permissions = [
            'manage_users' => 'Mengelola data pengguna, termasuk melihat daftar user, mengatur role, dan mengelola akses pengguna di panel admin.',
            'manage_courses' => 'Mengelola course, termasuk membuat, mengubah, menghapus, dan mengatur informasi course di website.',
            'manage_topics' => 'Mengelola topik pembelajaran, termasuk materi, struktur topik, dan workspace mentor pada course.',
            'access_topic' => 'Mengakses halaman topik, materi pembelajaran, dan konten belajar yang tersedia untuk peserta.',
            'enroll_course' => 'Mendaftar atau mengikuti course dari halaman katalog course maupun halaman detail course.',
            'take_assessment' => 'Mengerjakan assessment atau ujian yang tersedia setelah memenuhi syarat pada course.',
            'manage_assessments' => 'Mengelola assessment, soal, opsi jawaban, dan pengaturan evaluasi pembelajaran.',
            'manage_certificates' => 'Mengelola penerbitan, validasi, dan distribusi sertifikat untuk peserta yang memenuhi syarat.',
            'view_reports' => 'Melihat laporan, ringkasan progres, dan data monitoring pembelajaran pada dashboard atau halaman admin.',
        ];
        $permissionRows = [];
        foreach ($permissions as $name => $description) {
            $permissionRows[$name] = [
                'id' => $this->uuid(),
                'name' => $name,
                'description' => $description,
                'created_at' => $now,
                'updated_at' => $now,
            ];
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
            // ADMIN
            ['name' => 'System Administrator', 'email' => 'admin@example.test', 'gender' => 'male', 'phone' => '+62-811-0000-0001', 'dob' => '1990-01-01', 'roles' => ['admin']],
        ];

        for ($i = 1; $i <= 10; $i++) {
            $profiles[] = [
                'name' => "Murid {$i}",
                'email' => "murid{$i}@example.test",
                'gender' => $i % 2 === 0 ? 'female' : 'male',
                'phone' => sprintf('+62-811-1000-%04d', $i),
                'dob' => now()->subYears(18 + $i)->format('Y-m-d'),
                'roles' => ['student'],
            ];
        }

        $rows = [];
        $byEmail = [];
        $byRole = ['admin' => [], 'student' => [], 'disciples' => []];

        foreach ($profiles as $profile) {
            $row = [
                'id' => $this->uuid(),
                'name' => $profile['name'],
                'email' => $profile['email'],
                'email_verified_at' => $now->copy()->subDays(rand(1, 30)),
                'password' => Hash::make('test1234'),
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

    private function seedAdditionalLearningData(array $users, string $asset, $now): void
    {
        $teacher = $users['byRole']['admin'][0] ?? null;
        $students = $users['byRole']['student'] ?? [];

        if (!$teacher || $students === []) {
            return;
        }

        $courses = [];
        $topics = [];
        $sessions = [];
        $enrollments = [];
        $attendances = [];

        $courseBlueprints = [
            [
                'title' => 'Pemuridan Dasar Kristus',
                'slug' => 'pemuridan-dasar-kristus',
                'description' => 'Mengenal panggilan menjadi murid Kristus, dasar pertobatan, dan hidup yang berakar dalam Injil.',
                'prefix' => 'PDM',
                'number' => 101,
                'topics' => [
                    'Identitas Murid Kristus',
                    'Pertobatan dan Kelahiran Baru',
                    'Dasar Hidup Dalam Injil',
                ],
            ],
            [
                'title' => 'Formasi Karakter Murid',
                'slug' => 'formasi-karakter-murid',
                'description' => 'Membangun karakter murid yang setia melalui disiplin rohani, ketaatan, dan integritas hidup.',
                'prefix' => 'FKM',
                'number' => 102,
                'topics' => [
                    'Disiplin Rohani Sehari-hari',
                    'Ketaatan Dalam Hal Kecil',
                    'Integritas dan Kekudusan',
                ],
            ],
            [
                'title' => 'Pemuridan Dalam Komunitas',
                'slug' => 'pemuridan-dalam-komunitas',
                'description' => 'Belajar bertumbuh bersama, saling menggembalakan, dan membangun relasi yang sehat dalam tubuh Kristus.',
                'prefix' => 'PDK',
                'number' => 103,
                'topics' => [
                    'Hidup Dalam Persekutuan',
                    'Saling Menasehati Dalam Kasih',
                    'Melayani Bersama Sebagai Tubuh Kristus',
                ],
            ],
            [
                'title' => 'Pemuridan dan Pengutusan',
                'slug' => 'pemuridan-dan-pengutusan',
                'description' => 'Menyiapkan murid untuk bersaksi, memuridkan orang lain, dan hidup sebagai terang di tengah dunia.',
                'prefix' => 'PDP',
                'number' => 104,
                'topics' => [
                    'Hati Seorang Pengutus',
                    'Murid yang Bersaksi',
                    'Memuridkan Generasi Berikutnya',
                ],
                'future_sessions' => 2,
            ],
        ];

        $assessmentPayloads = [];

        foreach ($courseBlueprints as $courseIndex => $blueprint) {
            $courseId = $this->uuid();
            $courseCreatedAt = $now->copy()->subDays(40 - ($courseIndex * 4));

            $courses[] = [
                'id' => $courseId,
                'title' => $blueprint['title'],
                'slug' => $blueprint['slug'],
                'poster' => $asset,
                'description' => $blueprint['description'],
                'status' => 'active',
                'certificate_course_number' => $blueprint['number'],
                'certificate_prefix_code' => $blueprint['prefix'],
                'created_at' => $courseCreatedAt,
                'updated_at' => $now,
            ];

            foreach ($blueprint['topics'] as $topicIndex => $topicName) {
                $topicId = $this->uuid();
                $topicCreatedAt = $courseCreatedAt->copy()->addDays($topicIndex);
                $pastStart = $now->copy()->subDays(($courseIndex * 6) + ($topicIndex + 2))->setTime(19, 0);
                $pastEnd = $pastStart->copy()->addMinutes(90);

                $topics[] = [
                    'id' => $topicId,
                    'course_id' => $courseId,
                    'teacher_id' => $teacher['id'],
                    'name' => $topicName,
                    'slug' => Str::slug($topicName . '-' . ($courseIndex + 1)),
                    'category' => 'pemuridan',
                    'description' => 'Topik pemuridan yang menolong peserta memahami dan mempraktikkan ' . strtolower($topicName) . '.',
                    'poster' => $asset,
                    'visibility' => 'public',
                    'status' => 'published',
                    'sort_order' => $topicIndex + 1,
                    'created_at' => $topicCreatedAt,
                    'updated_at' => $now,
                ];

                $sessions[] = [
                    'id' => $this->uuid(),
                    'topic_id' => $topicId,
                    'title' => 'Sesi ' . $topicName,
                    'zoom_link' => 'https://zoom.example.test/' . $blueprint['slug'] . '-topic-' . ($topicIndex + 1),
                    'record_link' => null,
                    'start_at' => $pastStart,
                    'end_at' => $pastEnd,
                    'reminder_sent_at' => $pastStart->copy()->subMinutes(30),
                    'status' => 'completed',
                    'created_at' => $topicCreatedAt,
                    'updated_at' => $now,
                ];

                foreach ($students as $studentIndex => $student) {
                    $attendanceStatus = $studentIndex % 4 === 0 ? 'late' : 'present';
                    $attendances[] = [
                        'id' => $this->uuid(),
                        'video_session_id' => $sessions[array_key_last($sessions)]['id'],
                        'user_id' => $student['id'],
                        'status' => $attendanceStatus,
                        'check_in_at' => $pastStart->copy()->addMinutes(($studentIndex % 3) * 4),
                        'clock_out_at' => $pastEnd->copy()->subMinutes($studentIndex % 3),
                        'ip_address' => '127.0.0.1',
                        'created_at' => $pastStart,
                        'updated_at' => $now,
                    ];
                }
            }

            if (($blueprint['future_sessions'] ?? 0) > 0) {
                $targetTopicId = $topics[array_key_last($topics)]['id'];

                for ($futureIndex = 1; $futureIndex <= $blueprint['future_sessions']; $futureIndex++) {
                    $futureStart = $now->copy()->addDays($futureIndex + 2)->setTime(19, 0);
                    $sessions[] = [
                        'id' => $this->uuid(),
                        'topic_id' => $targetTopicId,
                        'title' => 'Sesi Lanjutan Pengutusan ' . $futureIndex,
                        'zoom_link' => 'https://zoom.example.test/' . $blueprint['slug'] . '-future-' . $futureIndex,
                        'record_link' => null,
                        'start_at' => $futureStart,
                        'end_at' => $futureStart->copy()->addMinutes(90),
                        'reminder_sent_at' => null,
                        'status' => 'scheduled',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach ($students as $studentIndex => $student) {
                $enrollments[] = [
                    'id' => $this->uuid(),
                    'user_id' => $student['id'],
                    'course_id' => $courseId,
                    'status' => 'active',
                    'enrolled_at' => $courseCreatedAt->copy()->addDay(),
                    'completed_at' => null,
                    'created_at' => $courseCreatedAt,
                    'updated_at' => $now,
                ];
            }

            $assessmentPayloads[] = [
                'course_id' => $courseId,
                'course_title' => $blueprint['title'],
                'created_at' => $courseCreatedAt->copy()->addDays(5),
            ];
        }

        DB::table('courses')->insert($courses);
        DB::table('topics')->insert($topics);
        DB::table('video_sessions')->insert($sessions);
        DB::table('course_enrollments')->insert($enrollments);
        DB::table('attendances')->insert($attendances);
        $this->seedAssessmentsForCourses($assessmentPayloads, $now);
    }

    private function seedAssessmentsForCourses(array $assessmentPayloads, $now): void
    {
        $assessments = [];
        $questions = [];
        $questionOptions = [];

        foreach ($assessmentPayloads as $courseIndex => $payload) {
            $assessmentId = $this->uuid();
            $assessments[] = [
                'id' => $assessmentId,
                'course_id' => $payload['course_id'],
                'title' => 'Assessment ' . $payload['course_title'],
                'passing_grade' => 70,
                'randomize_questions' => false,
                'question_limit' => 5,
                'status' => 'active',
                'created_at' => $payload['created_at'],
                'updated_at' => $now,
            ];

            for ($questionNumber = 1; $questionNumber <= 5; $questionNumber++) {
                $questionId = $this->uuid();
                $questions[] = [
                    'id' => $questionId,
                    'assessment_id' => $assessmentId,
                    'question_type' => 'mcq',
                    'question' => 'Dalam ' . $payload['course_title'] . ', apa inti pemuridan pada soal nomor ' . $questionNumber . '?',
                    'correct_text_answer' => null,
                    'explanation' => 'Peserta diharapkan memahami prinsip pemuridan yang diajarkan di course ini.',
                    'sort_order' => $questionNumber,
                    'created_at' => $payload['created_at'],
                    'updated_at' => $now,
                ];

                $questionOptions[] = [
                    'id' => $this->uuid(),
                    'question_id' => $questionId,
                    'option_text' => 'Hidup taat kepada Kristus dan memuridkan orang lain.',
                    'is_correct' => true,
                    'sort_order' => 1,
                    'created_at' => $payload['created_at'],
                    'updated_at' => $now,
                ];
                $questionOptions[] = [
                    'id' => $this->uuid(),
                    'question_id' => $questionId,
                    'option_text' => 'Berfokus pada pengetahuan tanpa ketaatan.',
                    'is_correct' => false,
                    'sort_order' => 2,
                    'created_at' => $payload['created_at'],
                    'updated_at' => $now,
                ];
                $questionOptions[] = [
                    'id' => $this->uuid(),
                    'question_id' => $questionId,
                    'option_text' => 'Menjalani iman sendirian tanpa komunitas.',
                    'is_correct' => false,
                    'sort_order' => 3,
                    'created_at' => $payload['created_at'],
                    'updated_at' => $now,
                ];
                $questionOptions[] = [
                    'id' => $this->uuid(),
                    'question_id' => $questionId,
                    'option_text' => 'Menghindari pertumbuhan dan pengutusan.',
                    'is_correct' => false,
                    'sort_order' => 4,
                    'created_at' => $payload['created_at'],
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('assessments')->insert($assessments);
        DB::table('questions')->insert($questions);
        DB::table('question_options')->insert($questionOptions);
    }
}
