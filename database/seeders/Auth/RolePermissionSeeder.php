<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'admin' => [
                'manage_users',
                'manage_courses',
                'manage_topics',
                'manage_assessments',
                'manage_certificates',
                'view_reports',
            ],
            'student' => [
                'enroll_course',
                'access_topic',
                'take_assessment',
            ],
            'disciples' => [
                'access_topic',
                'manage_topics',
                'manage_assessments',
                'view_reports',
            ],
        ];

        foreach ($map as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                continue;
            }

            $permissionIds = Permission::whereIn('name', $permissionNames)
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}