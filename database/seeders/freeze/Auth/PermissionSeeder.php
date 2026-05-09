<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage_users',
            'manage_courses',
            'manage_topics',
            'access_topic',
            'enroll_course',
            'take_assessment',
            'manage_assessments',
            'manage_certificates',
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                [
                    'id' => (string) Str::uuid(),
                ]
            );
        }
    }
}