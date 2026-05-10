<?php

namespace App\Livewire\Mentor\Topics;

use App\Livewire\Concerns\WithTableState;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use Livewire\Component;

class TopicIndex extends Component
{
    use WithTableState;

    public function render()
    {
        $userId = auth()->id();

        $managedTopicIds = TopicUser::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('role_type', 'owner')
                    ->orWhereHas('permissions', function ($p) {
                        $p->where('permission', 'manage_topics');
                    });
            })
            ->pluck('topic_id');

        $topics = Topic::query()
            ->with(['course.studyProgram', 'course.assessment'])
            ->withCount(['materials'])
            ->where(function ($q) use ($userId, $managedTopicIds) {
                $q->where('teacher_id', $userId);

                if ($managedTopicIds->isNotEmpty()) {
                    $q->orWhereIn('id', $managedTopicIds);
                }
            })
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        $topicIds = $topics->pluck('id')->all();

        $studentCounts = collect();

        if (!empty($topicIds)) {
            $studentCounts = TopicProgress::query()
                ->whereIn('topic_id', $topicIds)
                ->selectRaw('topic_id, COUNT(DISTINCT course_enrollment_id) as student_count')
                ->groupBy('topic_id')
                ->pluck('student_count', 'topic_id');
        }

        return view('livewire.mentor.topics.index', [
            'topics' => $topics,
            'studentCounts' => $studentCounts,
        ])->layout('layouts.learning');
    }
}