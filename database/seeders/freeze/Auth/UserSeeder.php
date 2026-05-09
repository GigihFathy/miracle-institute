<?php

namespace Database\Seeders\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
         |-------------------------------------------------------------
         | Testing credentials:
         | 1) Admin     : admin@example.test    / Password123!
         | 2) Disciple  : disciple@example.test / Password123!
         | 3) Student   : student@example.test  / Password123!
         |-------------------------------------------------------------
         */

        $adminRole = Role::where('name', 'admin')->first();
        $studentRole = Role::where('name', 'student')->first();
        $disciplesRole = Role::where('name', 'disciples')->first();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
        ]);
        if ($adminRole) $admin->roles()->attach($adminRole->id, ['assigned_at' => now()]);

        $grace = User::factory()->create([
            'name' => 'Grace',
            'email' => 'disciple@example.test',
        ]);
        if ($disciplesRole) $grace->roles()->attach($disciplesRole->id, ['assigned_at' => now()]);
        if ($studentRole) $grace->roles()->attach($studentRole->id, ['assigned_at' => now()]);

        $alex = User::factory()->create([
            'name' => 'Alex',
            'email' => 'student@example.test',
        ]);
        if ($studentRole) $alex->roles()->attach($studentRole->id, ['assigned_at' => now()]);

        if ($studentRole) {
            User::factory()->count(8)->student()->create()->each(function ($user) use ($studentRole) {
                $user->roles()->attach($studentRole->id, ['assigned_at' => now()]);
            });
        }

        User::factory()->count(2)->disciples()->create()->each(function ($user) use ($studentRole, $disciplesRole) {
            if ($studentRole) $user->roles()->attach($studentRole->id, ['assigned_at' => now()]);
            if ($disciplesRole) $user->roles()->attach($disciplesRole->id, ['assigned_at' => now()]);
        });
    }
}