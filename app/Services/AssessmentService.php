<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\CourseEnrollment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AssessmentService
{
    public function prepareAttempt(Assessment $assessment): array
    {
        $questions = $assessment->questions()
            ->with([
                'options' => fn ($query) => $query->orderBy('sort_order')
            ])
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
                    'question_type' => $question->question_type
                        ?? ($question->options->isNotEmpty() ? 'mcq' : 'text'),

                    'correct_text_answer' => $question->correct_text_answer
                        ?? $question->answer
                        ?? null,

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

            'time_limit_minutes' => $assessment->time_limit_minutes,
        ];
    }

    public function prepareTakerQuestions(Assessment $assessment): Collection
    {
        $questions = $assessment->questions()
            ->with([
                'options' => fn ($query) => $query->orderBy('sort_order')
            ])
            ->orderBy('sort_order')
            ->get()
            ->values();

        if ($assessment->randomize_questions) {
            $questions = $questions->shuffle()->values();
        }

        if ($assessment->question_limit) {
            $questions = $questions->take($assessment->question_limit)->values();
        }

        return $questions;
    }

    public function resolveAttempt(
        Assessment $assessment,
        User $user
    ): AssessmentAttempt {
        $lastAttempt = AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->latest('attempt_no')
            ->first();

        if ($lastAttempt && ! $lastAttempt->submitted_at) {
            return $lastAttempt;
        }

        $attemptNo = $lastAttempt
            ? $lastAttempt->attempt_no + 1
            : 1;

        return AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_no' => $attemptNo,
            'started_at' => now(),
        ]);
    }

    public function loadAttemptAnswers(
        AssessmentAttempt $attempt
    ): array {
        return $attempt->answers()
            ->get()
            ->mapWithKeys(fn ($answer) => [
                $answer->question_id =>
                    $answer->question_option_id
                    ?? $answer->answer_text,
            ])
            ->toArray();
    }

    public function calculateRemainingSeconds(
        AssessmentAttempt $attempt,
        ?int $timeLimitMinutes
    ): ?int {
        if (! $timeLimitMinutes || ! $attempt->started_at) {
            return null;
        }

        $endTime = $attempt->started_at
            ->copy()
            ->addMinutes($timeLimitMinutes);

        return max(
            0,
            now()->diffInSeconds($endTime, false)
        );
    }

    public function syncAttemptAnswer(
        AssessmentAttempt $attempt,
        Question $question,
        mixed $value
    ): void {
        if ($attempt->submitted_at) {
            return;
        }

        $type = $question->question_type
            ?? ($question->options->isNotEmpty() ? 'mcq' : 'text');

        if ($type === 'mcq') {

            if (blank($value)) {
                AssessmentAnswer::query()
                    ->where('attempt_id', $attempt->id)
                    ->where('question_id', $question->id)
                    ->delete();

                return;
            }

            AssessmentAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_option_id' => $value,
                    'answer_text' => null,
                    'is_correct' => null,
                ]
            );

            return;
        }

        $value = trim((string) $value);

        if ($value === '') {

            AssessmentAnswer::query()
                ->where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->delete();

            return;
        }

        AssessmentAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'question_option_id' => null,
                'answer_text' => $value,
                'is_correct' => null,
            ]
        );
    }

    public function finalizeAttempt(
        AssessmentAttempt $attempt,
        ?Collection $questions = null
    ): array {
        return DB::transaction(function () use ($attempt, $questions) {

            $attempt->loadMissing([
                'assessment',
                'user',
            ]);

            $assessment = $attempt->assessment;

            $questions ??= $assessment->questions()
                ->with('options')
                ->orderBy('sort_order')
                ->get()
                ->values();

            $answers = $attempt->answers()
                ->with('question.options')
                ->get()
                ->keyBy('question_id');

            $correct = 0;

            foreach ($questions as $question) {

                $answer = $answers->get($question->id);

                $isCorrect = $this->isAnswerCorrect(
                    $question,
                    $answer
                );

                if ($answer) {
                    $answer->update([
                        'is_correct' => $isCorrect,
                    ]);
                }

                if ($isCorrect) {
                    $correct++;
                }
            }

            $totalQuestions = $questions->count();

            $score = $totalQuestions > 0
                ? (int) round(($correct / $totalQuestions) * 100)
                : 0;

            $passed = $score >= (int) $assessment->passing_grade;

            $attempt->update([
                'score' => $score,
                'passed' => $passed,
                'submitted_at' => now(),
            ]);


            return [
                'score' => $score,
                'passed' => $passed,
                'correct' => $correct,
                'total' => $totalQuestions,
            ];
        });
    }

    private function isAnswerCorrect(
        Question $question,
        ?AssessmentAnswer $answer
    ): bool {
        if (! $answer) {
            return false;
        }

        $type = $question->question_type
            ?? ($question->options->isNotEmpty() ? 'mcq' : 'text');

        if ($type === 'mcq') {

            $correctOption = $question->options
                ->firstWhere('is_correct', true);

            return $correctOption
                && (string) $answer->question_option_id
                    === (string) $correctOption->id;
        }

        $expected = strtolower(
            trim(
                (string) (
                    $question->correct_text_answer
                    ?? $question->answer
                    ?? ''
                )
            )
        );

        $given = strtolower(
            trim((string) $answer->answer_text)
        );

        return $expected !== ''
            && $expected === $given;
    }

    public function submitAttempt(
        User $user,
        Assessment $assessment,
        array $questionIds,
        array $answers
    ): array {
        return DB::transaction(function () use (
            $user,
            $assessment,
            $questionIds,
            $answers
        ) {

            CourseEnrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $assessment->course_id)
                ->firstOrFail();

            $questions = Question::query()
                ->with('options')
                ->whereIn('id', $questionIds)
                ->get();

            $attemptNo = AssessmentAttempt::query()
                    ->where('assessment_id', $assessment->id)
                    ->where('user_id', $user->id)
                    ->count() + 1;

            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'user_id' => $user->id,
                'attempt_no' => $attemptNo,
                'started_at' => now(),
                'submitted_at' => now(),
            ]);

            foreach ($questions as $question) {

                $selected = $answers[$question->id] ?? null;

                $type = $question->question_type
                    ?? ($question->options->isNotEmpty() ? 'mcq' : 'text');

                $isCorrect = false;

                if ($type === 'mcq') {

                    $correctOption = $question->options
                        ->firstWhere('is_correct', true);

                    $isCorrect = $selected
                        && $correctOption
                        && $selected === $correctOption->id;

                } else {

                    $expected = strtolower(
                        trim(
                            (string) (
                                $question->correct_text_answer
                                ?? $question->answer
                                ?? ''
                            )
                        )
                    );

                    $given = strtolower(
                        trim((string) $selected)
                    );

                    $isCorrect = $expected !== ''
                        && $expected === $given;
                }

                AssessmentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'question_option_id' => $type === 'mcq'
                        ? $selected
                        : null,

                    'answer_text' => $type === 'text'
                        ? $selected
                        : null,

                    'is_correct' => $isCorrect,
                ]);
            }

            $result = $this->finalizeAttempt($attempt);

            return [
                'attempt_id' => $attempt->id,
                ...$result,
            ];
        });
    }
}