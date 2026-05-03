<?php

namespace App\Livewire\Assessments;

use App\Models\AssessmentAttempt;
use Livewire\Component;

class AssessmentResult extends Component
{
    public AssessmentAttempt $attempt;
    public $assessment;

    public function mount(AssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $attempt->load('assessment');

        $this->attempt = $attempt;
        $this->assessment = $attempt->assessment;
    }

    public function render()
    {
        return view('livewire.assessments.result')->layout('layouts.learning');
    }
}