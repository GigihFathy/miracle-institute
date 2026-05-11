<?php

namespace App\Services;

use App\Events\AssessmentPassed;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\CourseEnrollment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssessmentService
{
    public function prepareAttempt(Assessment $assessment): array
    {
        $questions = $assessment->questions()
            ->with(['options' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        if ($assessment->randomize_questions) {
            $questions = $questions->shuffle()->values();
        }

        if ($assessment->question_limit) {
            $questions = $questions->take($assessment->question_limit)->values();
        }

        return [
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type ?? ($question->options->isNotEmpty() ? 'mcq' : 'text'),
                    'correct_text_answer' => $question->correct_text_answer ?? $question->answer ?? null,
                    'options' => $question->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'option_key' => $option->option_key,
                            'option_text' => $option->option_text,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
            'question_ids' => $questions->pluck('id')->values()->all(),
        ];
    }

    public function submitAttempt(User $user, Assessment $assessment, array $questionIds, array $answers): array
    {
        return DB::transaction(function () use ($user, $assessment, $questionIds, $answers) {
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $assessment->topic->course_id)
                ->firstOrFail();

            $questions = Question::with('options')
                ->whereIn('id', $questionIds)
                ->get();

            $total = $questions->count();
            $correct = 0;

            $attemptNo = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('user_id', $user->id)
                ->count() + 1;

            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'user_id' => $user->id,
                'attempt_no' => $attemptNo,
                'score' => 0,
                'passed' => false,
                'started_at' => now(),
                'submitted_at' => now(),
            ]);

            $details = [];

            foreach ($questions as $question) {
                $selected = $answers[$question->id] ?? null;
                $type = $question->question_type ?? ($question->options->isNotEmpty() ? 'mcq' : 'text');

                $isCorrect = false;

                if ($type === 'mcq') {
                    $correctOption = $question->options->firstWhere('is_correct', true);
                    $isCorrect = $selected && $correctOption && $selected === $correctOption->id;
                } else {
                    $expected = strtolower(trim((string) ($question->correct_text_answer ?? $question->answer ?? '')));
                    $given = strtolower(trim((string) $selected));
                    $isCorrect = $expected !== '' && $expected === $given;
                }

                if ($isCorrect) {
                    $correct++;
                }

                AssessmentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'question_option_id' => $type === 'mcq' ? $selected : null,
                    'answer_text' => $type === 'text' ? $selected : null,
                    'is_correct' => $isCorrect,
                ]);

                $details[] = [
                    'question_id' => $question->id,
                    'type' => $type,
                    'selected' => $selected,
                    'is_correct' => $isCorrect,
                ];
            }

            $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
            $passed = $score >= $assessment->passing_grade;

            $attempt->update([
                'score' => $score,
                'passed' => $passed,
            ]);

            if ($passed) {
                event(new AssessmentPassed(
                    $user->id,
                    $assessment->id,
                    $assessment->topic_id,
                    $assessment->topic->course_id,
                    $attempt->id,
                    $score
                ));
            }

            return [
                'attempt_id' => $attempt->id,
                'score' => $score,
                'passed' => $passed,
                'total' => $total,
                'correct' => $correct,
                'details' => $details,
            ];
        });
    }
}