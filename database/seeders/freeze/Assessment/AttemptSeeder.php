<?php

namespace Database\Seeders\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentAnswer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttemptSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('email', [
            'disciple@example.test',
            'student@example.test'
        ])->get();

        foreach ($users as $user) {
            foreach (Assessment::all() as $assessment) {

                $questions = Question::with('options')
                    ->where('assessment_id', $assessment->id)
                    ->get();

                $attempt = AssessmentAttempt::create([
                    'assessment_id' => $assessment->id,
                    'user_id' => $user->id,
                    'attempt_no' => 1,
                    'started_at' => now()->subDays(2),
                    'submitted_at' => now()->subDays(2)->addMinutes(20),
                ]);

                $correctCount = 0;

                foreach ($questions as $index => $question) {

                    $correctOption = $question->options->firstWhere('is_correct', true);

                    $isSmartUser = $user->email === 'disciple@example.test';

                    $selected = $isSmartUser || $index < 2
                        ? $correctOption
                        : $question->options->firstWhere('id', '!=', $correctOption->id);

                    $isCorrect = $selected && $correctOption && $selected->id === $correctOption->id;

                    if ($isCorrect) {
                        $correctCount++;
                    }

                    AssessmentAnswer::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'question_option_id' => $selected?->id,
                        'answer_text' => null,
                        'is_correct' => $isCorrect,
                    ]);
                }

                $score = $questions->count()
                    ? (int) round(($correctCount / $questions->count()) * 100)
                    : 0;

                $attempt->update([
                    'score' => $score,
                    'passed' => $score >= $assessment->passing_grade,
                ]);
            }
        }
    }
}