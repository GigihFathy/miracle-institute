<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentFlowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssessmentTaker extends Component
{
    use AuthorizesRequests;

    public Assessment $assessment;
    public AssessmentAttempt $attempt;

    public array $questions = [];
    public array $answers = [];

    public int $currentIndex = 0;

    public bool $openSubmit = false;

    public function mount(Assessment $assessment, AssessmentFlowService $flowService): void
    {
        abort_unless(Auth::check(), 403);

        $this->assessment = $assessment->loadMissing(['course']);

        $passedAttempt = $flowService->latestPassedAttempt($this->assessment->id, Auth::id());
        if ($passedAttempt) {
            redirect()->route('assessments.result', $passedAttempt->id);
            return;
        }

        $this->attempt = $flowService->startAttempt($this->assessment->id, Auth::id());

        if ($this->attempt->submitted_at) {
            redirect()->route('assessments.result', $this->attempt->id);
            return;
        }

        $this->questions = $flowService->loadAttemptQuestions($this->attempt)
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type,
                    'options' => $question->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();

        $this->loadAnswers();
    }

    private function loadAnswers(): void
    {
        $this->answers = $this->attempt->answers()
            ->get()
            ->mapWithKeys(fn ($answer) => [
                $answer->question_id => $answer->question_option_id,
            ])
            ->all();
    }

    public function selectOption(string $questionId, string $optionId, AssessmentFlowService $flowService): void
    {
        if ($this->attempt->submitted_at) {
            return;
        }

        $flowService->saveAnswer($this->attempt, $questionId, $optionId);
        $this->answers[$questionId] = $optionId;
    }

    public function clearOption(string $questionId, AssessmentFlowService $flowService): void
    {
        if ($this->attempt->submitted_at) {
            return;
        }

        $flowService->clearAnswer($this->attempt, $questionId);
        unset($this->answers[$questionId]);
    }

    public function next(): void
    {
        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;
        }
    }

    public function prev(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function goTo(int $index): void
    {
        if (isset($this->questions[$index])) {
            $this->currentIndex = $index;
        }
    }

    public function submit(AssessmentFlowService $flowService, bool $auto = false): void
    {
        if ($this->attempt->submitted_at) {
            redirect()->route('assessments.result', $this->attempt->id);
            return;
        }

        $submitted = $flowService->submitAttempt($this->attempt, $auto);

        $this->openSubmit = false;

        session()->flash(
            'success',
            $submitted->passed
                ? 'Selamat, langkah ini telah tuntas! Sertifikat dan catatan progresmu sudah sinkron. Teruslah bertumbuh dan jadilah berkat bagi sesama dengan ilmu yang baru ini.'
                : 'Assessment belum lulus. Anda dapat mengulang attempt berikutnya sampai mencapai passing grade.'
        );

        redirect()->route('assessments.result', $submitted->id);
    }

    public function render()
    {
        return view('livewire.assessments.assessment-taker')->layout('layouts.learning');
    }
}