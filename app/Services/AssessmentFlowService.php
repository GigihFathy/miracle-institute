<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\CourseEnrollment;
use App\Models\Question;
use App\Models\Topic;
use App\Models\TopicProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssessmentFlowService
{
    public function __construct(
        protected ProgressService $progressService
    ) {
    }

    public function latestAttempt(string $assessmentId, string $userId): ?AssessmentAttempt
    {
        return AssessmentAttempt::query()
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $userId)
            ->latest('attempt_no')
            ->first();
    }

    public function latestPassedAttempt(string $assessmentId, string $userId): ?AssessmentAttempt
    {
        return AssessmentAttempt::query()
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $userId)
            ->where('passed', true)
            ->whereNotNull('submitted_at')
            ->latest('attempt_no')
            ->first();
    }

    public function canRetake(string $assessmentId, string $userId): bool
    {
        $latestAttempt = $this->latestAttempt($assessmentId, $userId);

        if (!$latestAttempt) {
            return true;
        }

        return!$latestAttempt->passed;
    }

    public function startAttempt(string $assessmentId, string $userId): AssessmentAttempt
    {
        $assessment = Assessment::query()
            ->withCount('questions')
            ->findOrFail($assessmentId);

        if ($assessment->status !== 'active') {
            throw ValidationException::withMessages([
                'assessment' => 'Assessment tidak tersedia.',
            ]);
        }

        if ($assessment->available_from && now()->lt($assessment->available_from)) {
            throw ValidationException::withMessages([
                'assessment' => 'Soal belum dapat diakses. Tersedia mulai ' . $assessment->available_from->format('d M Y H:i') . '.',
            ]);
        }

        $existingPassed = $this->latestPassedAttempt($assessmentId, $userId);
        if ($existingPassed) {
            return $existingPassed;
        }

        $this->ensureEligibleToAccessAssessment($assessment, $userId);

        $lock = Cache::lock("assessment:start:{$assessment->id}:{$userId}", 10);

        return $lock->block(5, function () use ($assessment, $userId) {
            return DB::transaction(function () use ($assessment, $userId) {
                $activeAttempt = AssessmentAttempt::query()
                    ->where('assessment_id', $assessment->id)
                    ->where('user_id', $userId)
                    ->whereNull('submitted_at')
                    ->latest('attempt_no')
                    ->lockForUpdate()
                    ->first();

                if ($activeAttempt) {
                    return $activeAttempt;
                }

                $latestAttempt = AssessmentAttempt::query()
                    ->where('assessment_id', $assessment->id)
                    ->where('user_id', $userId)
                    ->latest('attempt_no')
                    ->lockForUpdate()
                    ->first();

                $attemptNo = $latestAttempt ? $latestAttempt->attempt_no + 1 : 1;

                $questions = $this->resolveQuestions($assessment);

                return AssessmentAttempt::query()->create([
                    'assessment_id' => $assessment->id,
                    'user_id' => $userId,
                    'attempt_no' => $attemptNo,
                    'passing_grade' => $assessment->passing_grade,
                    'total_questions' => $questions->count(),
                    'question_snapshot' => $questions->pluck('id')->values()->all(),
                    'started_at' => now(),
                    'status' => 'in_progress',
                ]);
            });
        });
    }

    public function loadAttemptQuestions(AssessmentAttempt $attempt): Collection
    {
        $attempt->loadMissing('assessment');

        $snapshot = $attempt->question_snapshot ?? [];

        if (!is_array($snapshot) || empty($snapshot)) {
            $snapshot = $this->resolveQuestions($attempt->assessment)->pluck('id')->values()->all();

            $attempt->forceFill([
                'question_snapshot' => $snapshot,
                'total_questions' => count($snapshot),
            ])->save();
        }

        $questions = Question::query()
            ->with(['options' => fn ($q) => $q->orderBy('sort_order')])
            ->whereIn('id', $snapshot)
            ->get()
            ->keyBy('id');

        return collect($snapshot)
            ->map(fn ($questionId) => $questions->get($questionId))
            ->filter()
            ->values();
    }

    public function saveAnswer(AssessmentAttempt $attempt, string $questionId, string $optionId): AssessmentAnswer
    {
        if ($attempt->submitted_at) {
            throw ValidationException::withMessages([
                'attempt' => 'Assessment sudah disubmit.',
            ]);
        }

        $question = Question::query()
            ->with('options')
            ->findOrFail($questionId);

        if (!in_array($question->id, $attempt->question_snapshot ?? [], true)) {
            throw ValidationException::withMessages([
                'question' => 'Question tidak valid untuk attempt ini.',
            ]);
        }

        if ($question->question_type !== 'mcq') {
            throw ValidationException::withMessages([
                'question' => 'Assessment ini hanya mendukung soal Multiple Choice.',
            ]);
        }

        $validOption = $question->options->contains(fn ($option) => (string) $option->id === (string) $optionId);

        if (!$validOption) {
            throw ValidationException::withMessages([
                'option' => 'Pilihan jawaban tidak valid.',
            ]);
        }

        $correctOptionId = $question->options->firstWhere('is_correct', true)?->id;
        $isCorrect = (string) $correctOptionId === (string) $optionId;

        return AssessmentAnswer::query()->updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'question_option_id' => $optionId,
                'answer_text' => null,
                'is_correct' => $isCorrect,
            ]
        );
    }

    public function clearAnswer(AssessmentAttempt $attempt, string $questionId): void
    {
        if ($attempt->submitted_at) {
            throw ValidationException::withMessages([
                'attempt' => 'Assessment sudah disubmit.',
            ]);
        }

        AssessmentAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->delete();
    }

    public function submitAttempt(AssessmentAttempt $attempt, bool $auto = false): AssessmentAttempt
    {
        if ($attempt->submitted_at) {
            return $attempt;
        }

        $lock = Cache::lock("assessment:submit:{$attempt->id}", 10);

        return $lock->block(5, function () use ($attempt, $auto) {
            return DB::transaction(function () use ($attempt, $auto) {
                $attempt->refresh();

                if ($attempt->submitted_at) {
                    return $attempt;
                }

                $attempt->loadMissing([
                    'assessment',
                    'answers.question.options',
                ]);

                $questions = $this->loadAttemptQuestions($attempt);
                $answers = $attempt->answers->keyBy('question_id');

                $correctAnswers = 0;
                $wrongAnswers = 0;

                foreach ($questions as $question) {
                    $answer = $answers->get($question->id);

                    $correctOptionId = $question->options->firstWhere('is_correct', true)?->id;

                    $isCorrect = $answer
                        && $correctOptionId
                        && (string) $answer->question_option_id === (string) $correctOptionId;

                    AssessmentAnswer::query()->updateOrCreate(
                        [
                            'attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                        ],
                        [
                            'question_option_id' => $answer?->question_option_id,
                            'answer_text' => null,
                            'is_correct' => (bool) $isCorrect,
                        ]
                    );

                    if ($isCorrect) {
                        $correctAnswers++;
                    } else {
                        $wrongAnswers++;
                    }
                }

                $answeredCount = $questions->count() - $questions->filter(fn ($q) =>!$answers->has($q->id))->count();
                $unanswered = max(0, $attempt->total_questions - $answeredCount);

                $score = $attempt->total_questions > 0
                    ? (int) round(($correctAnswers / $attempt->total_questions) * 100)
                    : 0;

                $passed = $score >= $attempt->assessment->passing_grade;

                $attempt->forceFill([
                    'score' => $score,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $wrongAnswers,
                    'unanswered_questions' => $unanswered,
                    'passed' => $passed,
                    'status' => $auto ? 'auto_submitted' : 'submitted',
                    'submitted_at' => now(),
                    'graded_at' => now(),
                ])->save();

                $this->progressService->syncCertificateState(
                    $attempt->user_id,
                    $attempt->assessment->course_id
                );

                return $attempt->fresh([
                    'assessment',
                    'answers.question.options',
                ]);
            });
        });
    }

    public function ensureEligibleToAccessAssessment(Assessment $assessment, string $userId): void
    {
        $enrollment = CourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $assessment->course_id)
            ->first();

        if (!$enrollment) {
            throw ValidationException::withMessages([
                'course' => 'User belum terdaftar pada course ini.',
            ]);
        }

        $topicIds = Topic::query()
            ->where('course_id', $assessment->course_id)
            ->where('status', '!=', 'archived')
            ->pluck('id')
            ->all();

        $allTopicsCompleted = empty($topicIds)
            ? true
            : TopicProgress::query()
                ->where('course_enrollment_id', $enrollment->id)
                ->whereIn('topic_id', $topicIds)
                ->where('status', 'completed')
                ->count() === count($topicIds);

        if (!$allTopicsCompleted) {
            throw ValidationException::withMessages([
                'assessment' => 'Assessment baru bisa dimulai setelah seluruh topik selesai.',
            ]);
        }
    }

    private function resolveQuestions(Assessment $assessment): Collection
    {
        $query = $assessment->questions()
            ->with(['options' => fn ($q) => $q->orderBy('sort_order')])
            ->inRandomOrder();

        if ($assessment->question_limit) {
            $query->limit($assessment->question_limit);
        }

        $questions = $query->get();

        if ($questions->isEmpty()) {
            throw ValidationException::withMessages([
                'assessment' => 'Assessment belum memiliki soal.',
            ]);
        }

        foreach ($questions as $question) {
            if ($question->question_type !== 'mcq') {
                throw ValidationException::withMessages([
                    'assessment' => 'Saat ini hanya tipe soal Multiple Choice yang didukung.',
                ]);
            }

            if ($question->options->count() < 2) {
                throw ValidationException::withMessages([
                    'assessment' => "Soal '{$question->question}' harus memiliki minimal 2 opsi.",
                ]);
            }

            if ($question->options->where('is_correct', true)->count() !== 1) {
                throw ValidationException::withMessages([
                    'assessment' => "Soal '{$question->question}' harus memiliki tepat 1 jawaban benar.",
                ]);
            }
        }

        return $questions->values();
    }
}