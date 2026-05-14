<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Livewire\Concerns\WithTableState;
use App\Models\CourseEnrollment;
use App\Models\Topic;
use Livewire\Component;

class StudentsTab extends Component
{
    use InteractsWithMentorTopic;
    use WithTableState;

    public Topic $topic;

    public bool $canManageStudents = false;

    protected string $pageName = 'studentsPage';

    public function updatedSearch(): void
    {
        $this->resetPage($this->pageName);
    }

    public function updatedPerPage(): void
    {
        $this->resetPage($this->pageName);
    }

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canAccessTopic($this->topic), 403);

        $this->canManageStudents = $this->hasWorkspacePermission(
            $this->topic,
            'manage_students'
        );
    }

    public function render()
    {
        $students = CourseEnrollment::query()
            ->with([
                'user',
                'topicProgresses' => function ($query) {
                    $query->where('topic_id', $this->topic->id);
                },
            ])
            ->where('course_id', $this->topic->course_id)
            ->when(filled($this->search), function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest('created_at')
            ->paginate($this->perPage, ['*'], $this->pageName);

        return view('livewire.mentor.topics.tabs.students-tab', [
            'students' => $students,
        ]);
    }
}