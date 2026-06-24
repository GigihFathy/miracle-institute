<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            $this->seedCompany($now);
            [$permissions, $roles] = $this->seedPermissionsAndRoles($now);
            $this->seedRolePermissions($roles, $permissions);
            $this->seedAdminUser($roles, $now);
        });
    }

    private function uuid(): string
    {
        return (string) Str::uuid();
    }

    private function seedCompany(\Carbon\Carbon $now): void
    {
        DB::table('companies')->insert([
            'id'          => $this->uuid(),
            'name'        => 'Miracle Institute',
            'description' => 'Platform pembelajaran organisasi pemuridan untuk content delivery, session scheduling, assessment, dan certification.',
            'address'     => null,
            'vision'      => null,
            'mission'     => null,
            'logo'        => null,
            'facebook'    => null,
            'instagram'   => null,
            'youtube'     => null,
            'email'       => null,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
    }

    private function seedPermissionsAndRoles(\Carbon\Carbon $now): array
    {
        $permissions = [
            'manage_users'        => 'Mengelola data pengguna, termasuk melihat daftar user, mengatur role, dan mengelola akses pengguna di panel admin.',
            'manage_courses'      => 'Mengelola course, termasuk membuat, mengubah, menghapus, dan mengatur informasi course di website.',
            'manage_topics'       => 'Mengelola topik pembelajaran, termasuk materi, struktur topik, dan workspace mentor pada course.',
            'access_topic'        => 'Mengakses halaman topik, materi pembelajaran, dan konten belajar yang tersedia untuk peserta.',
            'enroll_course'       => 'Mendaftar atau mengikuti course dari halaman katalog course maupun halaman detail course.',
            'take_assessment'     => 'Mengerjakan assessment atau ujian yang tersedia setelah memenuhi syarat pada course.',
            'manage_assessments'  => 'Mengelola assessment, soal, opsi jawaban, dan pengaturan evaluasi pembelajaran.',
            'manage_certificates' => 'Mengelola penerbitan, validasi, dan distribusi sertifikat untuk peserta yang memenuhi syarat.',
            'view_reports'        => 'Melihat laporan, ringkasan progres, dan data monitoring pembelajaran pada dashboard atau halaman admin.',
        ];

        $permissionRows = [];
        foreach ($permissions as $name => $description) {
            $permissionRows[$name] = [
                'id'          => $this->uuid(),
                'name'        => $name,
                'description' => $description,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }
        DB::table('permissions')->insert(array_values($permissionRows));

        $roles = [
            'admin'    => ['id' => $this->uuid(), 'name' => 'admin',    'label' => 'Admin',    'description' => 'Full control atas course, topic, assessment, certificate, dan report.', 'created_at' => $now, 'updated_at' => $now],
            'student'  => ['id' => $this->uuid(), 'name' => 'student',  'label' => 'Student',  'description' => 'Peserta pembelajaran yang mengakses materi, sesi, dan assessment.',      'created_at' => $now, 'updated_at' => $now],
            'disciples' => ['id' => $this->uuid(), 'name' => 'disciples', 'label' => 'Disciples', 'description' => 'Mentor/tutor yang juga dapat berperan sebagai student dan mengelola topik.', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('roles')->insert(array_values($roles));

        return [$permissionRows, $roles];
    }

    private function seedAdminUser(array $roles, \Carbon\Carbon $now): void
    {
        $password = 'm1r4cl3-1nst1tut3.1d';

        $userId = $this->uuid();
        DB::table('users')->insert([
            'id'                => $userId,
            'name'              => 'Administrator',
            'email'             => 'admin@miracle-institute.id',
            'email_verified_at' => $now,
            'password'          => Hash::make($password),
            'remember_token'    => Str::random(10),
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        DB::table('role_user')->insert([
            'user_id'     => $userId,
            'role_id'     => $roles['admin']['id'],
            'assigned_at' => $now,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->command->info('');
        $this->command->info('  Admin account created:');
        $this->command->info('  Email    : admin@miracle-institute.id');
        $this->command->info("  Password : {$password}");
        $this->command->info('  (Segera ganti password setelah login pertama)');
        $this->command->info('');
    }

    private function seedRolePermissions(array $roles, array $permissions): void
    {
        $map = [
            'admin'    => ['manage_users', 'manage_courses', 'manage_topics', 'manage_assessments', 'manage_certificates', 'view_reports'],
            'student'  => ['enroll_course', 'access_topic', 'take_assessment'],
            'disciples' => ['access_topic', 'manage_topics', 'manage_assessments', 'view_reports'],
        ];

        $rows = [];
        foreach ($map as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                $rows[] = [
                    'role_id'       => $roles[$roleName]['id'],
                    'permission_id' => $permissions[$permissionName]['id'],
                ];
            }
        }
        DB::table('role_permission')->insert($rows);
    }
}
