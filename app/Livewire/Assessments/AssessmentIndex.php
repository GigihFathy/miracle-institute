<?php

namespace App\Livewire\Assessments;

use App\Livewire\Concerns\WithTableState;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\CourseEnrollment;
use Livewire\Component;

class AssessmentIndex extends Component
{
    use WithTableState;

    public function render()
    {
        $user = auth()->user();
        $courseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->all();

        $assessments = Assessment::with('topic.course')
            ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        $latestAttempts = AssessmentAttempt::where('user_id', $user->id)
            ->latest()
            ->get()
            ->groupBy('assessment_id')
            ->map(fn ($rows) => $rows->first());

        return view('livewire.assessments.assessment-index', [
            'assessments' => $assessments,
            'latestAttempts' => $latestAttempts,
        ])->layout('layouts.learning');
    }
}