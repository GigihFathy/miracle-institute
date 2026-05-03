<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssessmentTaker extends Component
{
    public Assessment $assessment;
    public AssessmentAttempt $attempt;

    public $questions;
    public array $answers = [];

    public int $currentIndex = 0;

    public ?int $timeLeft = null;
    public ?int $timeLimit = null;

    public bool $openSubmit = false;

    protected $listeners = ['tick'];

    public function mount(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->attempt = $this->resolveAttempt();

        // redirect jika sudah submit
        if ($this->attempt->submitted_at) {
            return redirect()->route('assessments.result', $this->attempt->id);
        }

        $this->questions = $assessment->questions()
            ->with('options')
            ->orderBy('sort_order')
            ->get()
            ->values();

        $this->timeLimit = $assessment->time_limit_minutes;

        $this->loadAnswers();
        $this->initTimer();
    }

    private function resolveAttempt(): AssessmentAttempt
    {
        $last = AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('user_id', Auth::id())
            ->latest('attempt_no')
            ->first();

        if ($last && !$last->submitted_at) {
            return $last;
        }

        $attemptNo = $last ? $last->attempt_no + 1 : 1;

        return AssessmentAttempt::create([
            'assessment_id' => $this->assessment->id,
            'user_id' => Auth::id(),
            'attempt_no' => $attemptNo,
            'started_at' => now(),
        ]);
    }

    private function loadAnswers()
    {
        $this->answers = $this->attempt->answers()
            ->get()
            ->mapWithKeys(fn ($a) => [
                $a->question_id => $a->question_option_id ?? $a->answer_text
            ])
            ->toArray();
    }

    private function initTimer()
    {
        if (!$this->timeLimit || !$this->attempt->started_at) {
            return;
        }

        $end = $this->attempt->started_at->addMinutes($this->timeLimit);
        $this->timeLeft = now()->diffInSeconds($end, false);

        if ($this->timeLeft <= 0) {
            $this->submit();
        }
    }

    public function tick(): void
    {
        if ($this->timeLeft === null) return;

        $this->timeLeft--;

        if ($this->timeLeft <= 0) {
            $this->submit();
        }
    }

    public function getFormattedTimeProperty(): string
    {
        if ($this->timeLeft === null) return '';

        $m = floor($this->timeLeft / 60);
        $s = $this->timeLeft % 60;

        return sprintf('%02d:%02d', $m, $s);
    }

    public function updateAnswer($questionId, $value)
    {
        if ($this->attempt->submitted_at) return;

        $question = $this->questions->firstWhere('id', $questionId);
        if (!$question) return;

        if ($question->question_type === 'mcq') {
            AssessmentAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'question_option_id' => $value,
                    'answer_text' => null,
                ]
            );
        } else {
            AssessmentAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'question_option_id' => null,
                    'answer_text' => $value,
                ]
            );
        }

        $this->answers[$questionId] = $value;
    }

    public function submit()
    {
        if ($this->attempt->submitted_at) return;

        $answers = $this->attempt->answers()->with('question.options')->get();

        $correct = 0;

        foreach ($answers as $a) {
            $q = $a->question;

            if ($q->question_type === 'mcq') {
                $correctOption = $q->options->firstWhere('is_correct', true);
                $isCorrect = $correctOption && $a->question_option_id == $correctOption->id;
            } else {
                $isCorrect = strtolower(trim($a->answer_text)) === strtolower(trim($q->correct_text_answer));
            }

            $a->update(['is_correct' => $isCorrect]);

            if ($isCorrect) $correct++;
        }

        $total = $this->questions->count();

        $score = $total > 0
            ? round(($correct / $total) * 100)
            : 0;

        $this->attempt->update([
            'score' => $score,
            'passed' => $score >= $this->assessment->passing_grade,
            'submitted_at' => now(),
        ]);

        return redirect()->route('assessments.result', $this->attempt->id);
    }

    public function next()
    {
        if ($this->currentIndex < $this->questions->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function prev()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function goTo($index)
    {
        $this->currentIndex = $index;
    }

    public function saveTextAnswer(string $questionId, string $value): void
    {
        if ($this->attempt->submitted_at) return;

        $value = trim($value);

        if ($value === '') {
            unset($this->answers[$questionId]);

            AssessmentAnswer::where('attempt_id', $this->attempt->id)
                ->where('question_id', $questionId)
                ->delete();

            return;
        }

        $this->answers[$questionId] = $value;

        AssessmentAnswer::updateOrCreate(
            [
                'attempt_id' => $this->attempt->id,
                'question_id' => $questionId,
            ],
            [
                'answer_text' => $value,
                'question_option_id' => null,
            ]
        );
    }

    public function selectOption(string $questionId, string $optionId): void
    {
        if ($this->attempt->submitted_at) return;

        $this->answers[$questionId] = $optionId;

        AssessmentAnswer::updateOrCreate(
            [
                'attempt_id' => $this->attempt->id,
                'question_id' => $questionId,
            ],
            [
                'question_option_id' => $optionId,
                'answer_text' => null,
            ]
        );
    }

    public function render()
    {
        return view('livewire.assessments.assessment-taker')->layout('layouts.learning');
    }
}