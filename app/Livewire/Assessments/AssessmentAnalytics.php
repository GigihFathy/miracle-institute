<?php

namespace App\Livewire\Assessments;

use Livewire\Component;
use App\Models\AssessmentAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssessmentAnalytics extends Component
{
    public $assessmentId;

    public function render()
    {
        $stats = AssessmentAttempt::where('assessment_id', $this->assessmentId)
            ->selectRaw('
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                SUM(passed) as passed_count
            ')
            ->first();

        $best = AssessmentAttempt::where('assessment_id', $this->assessmentId)
            ->where('user_id', Auth::id())
            ->max('score');

        $passRate = $stats->total_attempts > 0
            ? round(($stats->passed_count / $stats->total_attempts) * 100)
            : 0;

        return view('livewire.assessments.analytics', [
            'stats' => $stats,
            'best' => $best,
            'passRate' => $passRate
        ])->layout('layouts.learning');
    }
}