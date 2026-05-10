<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Attendance;
use App\Models\Topic;
use Livewire\Component;

class AttendancesTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public bool $canManageAttendance = false;

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
            ->with(['user', 'videoSession'])
            ->whereHas('videoSession', function ($query) {
                $query->where('topic_id', $this->topic->id);
            })
            ->latest()
            ->get();

        return view('livewire.mentor.topics.tabs.attendances-tab', [
            'sessions' => $this->topic->videoSessions()->latest('start_at')->get(),
            'attendancesBySession' => $attendances->groupBy('video_session_id'),
        ]);
    }
}