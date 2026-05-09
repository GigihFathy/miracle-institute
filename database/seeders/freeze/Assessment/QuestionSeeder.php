<?php

namespace Database\Seeders\Assessment;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Assessment::all() as $assessment) {

            $questionCount = $assessment->question_limit ?? 5;

            for ($i = 1; $i <= $questionCount; $i++) {

                $question = Question::factory()
                    ->mcq()
                    ->create([
                        'assessment_id' => $assessment->id,
                        'sort_order' => $i,
                    ]);

                $correctIndex = rand(0, 3);

                for ($index = 0; $index < 4; $index++) {

                    QuestionOption::factory()->create([
                        'question_id' => $question->id,
                        'is_correct' => $index === $correctIndex,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
    }
}