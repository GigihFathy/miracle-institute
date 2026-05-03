<?php

namespace App\Livewire\Assessments;

use Livewire\Component;
use App\Models\AssessmentAttempt;
use Illuminate\Support\Facades\Auth;

class AttemptHistory extends Component
{
    public $assessmentId;

    public function render()
    {
        $attempts = AssessmentAttempt::where('assessment_id', $this->assessmentId)
            ->where('user_id', Auth::id())
            ->latest('attempt_no')
            ->get();

        return view('livewire.assessments.attempt-history', [
            'attempts' => $attempts
        ])->layout('layouts.learning');
    }
}