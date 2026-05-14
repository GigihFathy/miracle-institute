<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'id' => (string) Str::uuid(),
            'study_program_id' => StudyProgram::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'poster' => 'courses/default.jpg',
            'credit' => $this->faker->numberBetween(1, 6),
            'description' => $this->faker->paragraph(),
            'status' => 'active',
        ];
    }
}