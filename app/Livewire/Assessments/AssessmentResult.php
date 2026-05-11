<?php

namespace App\Livewire\Assessments;

use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use Livewire\Component;

class AssessmentResult extends Component
{
    public AssessmentAttempt $attempt;
    public $assessment;
    public ?Certificate $certificate = null;

    public int $correctAnswers = 0;
    public int $wrongAnswers = 0;
    public int $unansweredQuestions = 0;

    public function mount(AssessmentAttempt $attempt): void
    {
        abort_unless((string) $attempt->user_id === (string) auth()->id(), 403);
        abort_unless($attempt->submitted_at !== null, 404);

        $attempt->loadMissing([
            'assessment.course',
            'answers.question.options',
        ]);

        $this->attempt = $attempt;
        $this->assessment = $attempt->assessment;

        $this->correctAnswers = (int) $attempt->correct_answers;
        $this->wrongAnswers = (int) $attempt->wrong_answers;
        $this->unansweredQuestions = (int) $attempt->unanswered_questions;

        $this->certificate = Certificate::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $this->assessment->course_id)
            ->first();
    }

    public function render()
    {
        return view('livewire.assessments.result')->layout('layouts.learning');
    }
}