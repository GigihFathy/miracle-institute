<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Topic;
use Livewire\Component;

class OverviewTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public bool $canManageMaterials = false;
    public bool $canManageSessions = false;
    public bool $canManageStudents = false;

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canAccessTopic($this->topic), 403);

        $this->canManageMaterials = $this->hasWorkspacePermission(
            $this->topic,
            'manage_materials'
        );

        $this->canManageSessions = $this->hasWorkspacePermission(
            $this->topic,
            'manage_sessions'
        );

        $this->canManageStudents = $this->hasWorkspacePermission(
            $this->topic,
            'manage_students'
        );
    }

    public function render()
    {
        return view('livewire.mentor.topics.tabs.overview-tab', [
            'materialsCount' => $this->topic->materials()->count(),
            'sessionStatus' => optional(
                $this->topic->videoSessions()->latest('start_at')->first()
            )->status ?? 'not_available',
        ]);
    }
}