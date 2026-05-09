<?php

namespace Database\Seeders\Learning;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        $users = User::where('email', '!=', 'admin@example.test')->get();

        foreach ($users as $user) {
            foreach ($courses as $course) {
                CourseEnrollment::factory()->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => 'active',
                    'enrolled_at' => now()->subDays(10),
                    'completed_at' => null,
                ]);
            }
        }
    }
}