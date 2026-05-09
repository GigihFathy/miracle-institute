<?php

namespace Database\Seeders\Auth;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'admin', 'label' => 'Admin'],
            ['name' => 'student', 'label' => 'Student'],
            ['name' => 'disciples', 'label' => 'Disciples'],
            ['name' => 'instructor', 'label' => 'Instructor'],
        ] as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'id' => (string) Str::uuid(),
                    'label' => $role['label'],
                    'description' => null,
                ]
            );
        }
    }
}