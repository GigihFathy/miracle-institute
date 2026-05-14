<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Livewire\Concerns\WithTableState;
use App\Models\Attendance;
use App\Models\Topic;
use Livewire\Component;

class AttendancesTab extends Component
{
    use InteractsWithMentorTopic;
    use WithTableState;

    public Topic $topic;

    public bool $canManageAttendance = false;

    protected string $pageName = 'attendancesPage';

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

        $this->canManageAttendance = $this->hasWorkspacePermission(
            $this->topic,
            'manage_attendance'
        );
    }

    public function render()
    {
        $attendances = Attendance::query()
            ->with('user')
            ->whereHas('videoSession', function ($query) {
                $query->where('topic_id', $this->topic->id);
            })
            ->when(filled($this->search), function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage, ['*'], $this->pageName);

        return view('livewire.mentor.topics.tabs.attendances-tab', [
            'attendances' => $attendances,
        ]);
    }
}