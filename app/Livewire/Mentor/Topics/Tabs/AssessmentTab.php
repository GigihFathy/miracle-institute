<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Topic;
use Livewire\Component;

class AssessmentTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canManageTopic($this->topic), 403);
    }

    public function render()
    {
        return view('livewire.mentor.topics.tabs.assessment-tab', [
            'assessment' => $this->topic->course?->assessment,
        ]);
    }
}