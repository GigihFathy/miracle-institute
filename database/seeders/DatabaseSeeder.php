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
            $asset = 'images/logo.png';

            $this->seedCompany($asset, $now);
            [$permissions, $roles] = $this->seedPermissionsAndRoles($now);
            $this->seedRolePermissions($roles, $permissions);

            $users = $this->seedUsers($roles, $asset, $now);
            $program = $this->seedStudentStudyProgram($now);
            $this->seedAdditionalLearningData($program, $users, $asset, $now);
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
                'name' => "Siswa {$i}",
                'email' => "siswa{$i}@example.test",
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

    private function seedStudentStudyProgram($now): array
    {
        $program = [
            'id' => $this->uuid(),
            'title' => 'Program Siswa Tambahan',
            'slug' => 'program-siswa-tambahan',
            'description' => 'Program pembelajaran untuk data siswa dan course tambahan.',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('study_programs')->insert($program);

        return $program;
    }

    private function seedAdditionalLearningData(array $program, array $users, string $asset, $now): void
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

        for ($i = 1; $i <= 10; $i++) {
            $courseId = $this->uuid();
            $topicId = $this->uuid();
            $sessionId = $this->uuid();
            $courseCreatedAt = $now->copy()->subDays(20 - $i);
            $sessionStart = $now->copy()->subDays($i)->setTime(19, 0);
            $sessionEnd = $sessionStart->copy()->addMinutes(90);

            $courses[] = [
                'id' => $courseId,
                'study_program_id' => $program['id'],
                'title' => "Course Tambahan {$i}",
                'slug' => "course-tambahan-{$i}",
                'poster' => $asset,
                'description' => "Course tambahan {$i} untuk kebutuhan data dummy siswa, topik, sesi, dan kehadiran.",
                'status' => 'active',
                'created_at' => $courseCreatedAt,
                'updated_at' => $now,
            ];

            $topics[] = [
                'id' => $topicId,
                'course_id' => $courseId,
                'teacher_id' => $teacher['id'],
                'name' => "Topik Utama {$i}",
                'slug' => "topik-utama-{$i}",
                'category' => 'general',
                'description' => "Topik utama untuk Course Tambahan {$i}.",
                'poster' => $asset,
                'visibility' => 'public',
                'status' => 'published',
                'sort_order' => 1,
                'created_at' => $courseCreatedAt,
                'updated_at' => $now,
            ];

            $sessions[] = [
                'id' => $sessionId,
                'topic_id' => $topicId,
                'title' => "Sesi Live {$i}",
                'zoom_link' => "https://zoom.example.test/course-{$i}",
                'record_link' => null,
                'start_at' => $sessionStart,
                'end_at' => $sessionEnd,
                'reminder_sent_at' => $sessionStart->copy()->subMinutes(30),
                'status' => 'completed',
                'created_at' => $courseCreatedAt,
                'updated_at' => $now,
            ];

            foreach ($students as $index => $student) {
                $enrollmentId = $this->uuid();
                $checkInAt = $sessionStart->copy()->addMinutes(($index % 3) * 5);
                $clockOutAt = $sessionEnd->copy()->subMinutes(10 - ($index % 3));

                $enrollments[] = [
                    'id' => $enrollmentId,
                    'user_id' => $student['id'],
                    'course_id' => $courseId,
                    'status' => 'active',
                    'enrolled_at' => $courseCreatedAt->copy()->addDay(),
                    'completed_at' => null,
                    'created_at' => $courseCreatedAt,
                    'updated_at' => $now,
                ];

                $attendances[] = [
                    'id' => $this->uuid(),
                    'video_session_id' => $sessionId,
                    'user_id' => $student['id'],
                    'status' => $index % 4 === 0 ? 'late' : 'present',
                    'check_in_at' => $checkInAt,
                    'clock_out_at' => $clockOutAt,
                    'ip_address' => '127.0.0.1',
                    'created_at' => $sessionStart,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('courses')->insert($courses);
        DB::table('topics')->insert($topics);
        DB::table('video_sessions')->insert($sessions);
        DB::table('course_enrollments')->insert($enrollments);
        DB::table('attendances')->insert($attendances);
    }
}
