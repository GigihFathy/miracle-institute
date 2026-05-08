<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentService;
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

    protected AssessmentService $assessmentService;

    public function boot(AssessmentService $assessmentService): void
    {
        $this->assessmentService = $assessmentService;
    }

    public function mount(Assessment $assessment): void
    {
        if (! auth()->check()) {
            redirect()->route('login');
            return;
        }

        $this->assessment = $assessment;
        $this->attempt = $this->assessmentService->resolveAttempt($assessment, auth()->user());

        if ($this->attempt->submitted_at) {
            redirect()->route('assessments.result', $this->attempt->id);
            return;
        }

        $this->questions = $this->assessmentService->prepareTakerQuestions($assessment);
        $this->timeLimit = $assessment->time_limit_minutes;
        $this->answers = $this->assessmentService->loadAttemptAnswers($this->attempt);
        $this->timeLeft = $this->assessmentService->calculateRemainingSeconds($this->attempt, $this->timeLimit);

        if ($this->timeLeft !== null && $this->timeLeft <= 0) {
            $this->submit();
        }
    }

    public function tick(): void
    {
        if ($this->timeLeft === null) {
            $this->timeLeft = $this->assessmentService->calculateRemainingSeconds($this->attempt, $this->timeLimit);

            if ($this->timeLeft !== null && $this->timeLeft <= 0) {
                $this->submit();
            }

            return;
        }

        $this->timeLeft = $this->assessmentService->calculateRemainingSeconds($this->attempt, $this->timeLimit);

        if ($this->timeLeft !== null && $this->timeLeft <= 0) {
            $this->submit();
        }
    }

    public function getFormattedTimeProperty(): string
    {
        if ($this->timeLeft === null) {
            return '';
        }

        $minutes = floor($this->timeLeft / 60);
        $seconds = $this->timeLeft % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function updateAnswer($questionId, $value): void
    {
        if ($this->attempt->submitted_at) {
            return;
        }

        $question = $this->questions->firstWhere('id', $questionId);

        if (! $question) {
            return;
        }

        $this->assessmentService->syncAttemptAnswer($this->attempt, $question, $value);

        if (blank($value)) {
            unset($this->answers[$questionId]);
            return;
        }

        $this->answers[$questionId] = $value;
    }

    public function saveTextAnswer(string $questionId, string $value): void
    {
        $this->updateAnswer($questionId, $value);
    }

    public function selectOption(string $questionId, string $optionId): void
    {
        $this->updateAnswer($questionId, $optionId);
    }

    public function submit(): mixed
    {
        if ($this->attempt->submitted_at) {
            return redirect()->route('assessments.result', $this->attempt->id);
        }

        $this->assessmentService->finalizeAttempt($this->attempt, $this->questions);

        return redirect()->route('assessments.result', $this->attempt->id);
    }

    public function next(): void
    {
        if ($this->currentIndex < $this->questions->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function prev(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function goTo($index): void
    {
        $this->currentIndex = (int) $index;
    }

    public function render()
    {
        return view('livewire.assessments.assessment-taker')->layout('layouts.learning');
    }
}