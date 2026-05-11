<?php

namespace Database\Factories;

use App\Models\Assessment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),

            'course_id' => null,

            'title' => $this->faker->sentence(3) . ' Test',

            'passing_grade' => 70,

            'randomize_questions' => true,

            'question_limit' => $this->faker->randomElement([null, 5, 10]),


            'status' => 'active',
        ];
    }
}