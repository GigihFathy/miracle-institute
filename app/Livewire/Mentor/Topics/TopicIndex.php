<?php

namespace App\Livewire\Mentor\Topics;

use App\Livewire\Concerns\WithTableState;
use App\Models\Topic;
use App\Models\TopicProgress;
use Livewire\Component;

class TopicIndex extends Component
{
    use WithTableState;

    public function render()
    {
        $topics = Topic::with(['course', 'materials', 'assessments'])
            ->where('teacher_id', auth()->id())
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        $topicIds = $topics->pluck('id')->all();

        $studentCounts = [];
        foreach ($topicIds as $topicId) {
            $studentCounts[$topicId] = TopicProgress::where('topic_id', $topicId)
                ->distinct('course_enrollment_id')
                ->count('course_enrollment_id');
        }

        return view('livewire.mentor.topics.index', [
            'topics' => $topics,
            'studentCounts' => $studentCounts,
        ])->layout('layouts.learning');
    }
}