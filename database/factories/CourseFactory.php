<?php

namespace Database\Factories;

use App\Models\Course;
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
            'title' => $title,
            'slug' => Str::slug($title),
            'poster' => 'courses/default.jpg',
            'description' => $this->faker->paragraph(),
            'status' => 'active',
        ];
    }
}
