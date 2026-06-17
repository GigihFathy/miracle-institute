<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Livewire\Concerns\WithTableState;
use App\Models\Topic;
use Livewire\Component;

class AttendancesTab extends Component
{
    use InteractsWithMentorTopic;
    use WithTableState;

    public Topic $topic;

    public bool $canManageAttendance = false;
    public string $statusFilter = '';

    protected string $pageName = 'attendancesPage';

    public function updatedSearch(): void
    {
        $this->resetPage($this->pageName);
    }

    public function updatedPerPage(): void
    {
        $this->resetPage($this->pageName);
    }

    public function updatedStatusFilter(): void
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
        $attendances = $this->topic->attendances()
            ->with(['user', 'videoSession'])
            ->when(filled($this->search), function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when(filled($this->statusFilter), function ($query) {
                if ($this->statusFilter === 'online') {
                    $query->whereIn('attendances.status', ['online', 'absent']);
                    return;
                }

                $query->where('attendances.status', $this->statusFilter);
            })
            ->latest('check_in_at')
            ->latest()
            ->paginate($this->perPage, ['*'], $this->pageName);

        return view('livewire.mentor.topics.tabs.attendances-tab', [
            'attendances' => $attendances,
        ]);
    }
}
