<?php

namespace App\Livewire\Mentor\Topics;

use App\Livewire\Concerns\InteractsWithMentorTopic;;
use App\Models\Topic;
use Livewire\Component;

class TopicWorkspace extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public string $tab = 'overview';

    public function mount(string $slug): void
    {
        $this->topic = Topic::query()
            ->with([
                'course.assessment',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($this->canAccessTopic($this->topic), 403);

        $requestedTab = (string) request()->query('tab', $this->tab);
        $allowedTabs = array_keys($this->availableTabs());

        if (!in_array($requestedTab, $allowedTabs, true)) {
            $requestedTab = $allowedTabs[0] ?? 'overview';
        }

        $this->tab = $requestedTab;
    }

    public function setTab(string $tab): void
    {
        if (array_key_exists($tab, $this->availableTabs())) {
            $this->tab = $tab;
        }
    }

    private function availableTabs(): array
    {
        $tabs = [
            'overview' => 'Overview',
        ];

        if ($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics'])) {
            $tabs['materials'] = 'Materials';
        }

        if ($this->canAccessTopic($this->topic, ['manage_sessions', 'manage_topics'])) {
            $tabs['sessions'] = 'Sessions';
        }

        if ($this->canAccessTopic($this->topic, ['manage_attendance', 'view_reports', 'manage_topics'])) {
            $tabs['attendances'] = 'Attendances';
        }

        if ($this->canAccessTopic($this->topic, ['manage_students', 'view_reports', 'manage_topics'])) {
            $tabs['students'] = 'Students';
        }

        if ($this->canManageAssessmentForTopic($this->topic)) {
            $tabs['assessment'] = 'Assessment';
        }

        return $tabs;
    }

    public function render()
    {
        $tabs = $this->availableTabs();

        return view('livewire.mentor.topics.topic-workspace', [
            'tabs' => $tabs,
            'activeComponent' => match ($this->tab) {
                'materials' => \App\Livewire\Mentor\Topics\Tabs\MaterialsTab::class,
                'sessions' => \App\Livewire\Mentor\Topics\Tabs\SessionsTab::class,
                'attendances' => \App\Livewire\Mentor\Topics\Tabs\AttendancesTab::class,
                'students' => \App\Livewire\Mentor\Topics\Tabs\StudentsTab::class,
                'assessment' => \App\Livewire\Mentor\Topics\Tabs\AssessmentTab::class,
                default => \App\Livewire\Mentor\Topics\Tabs\OverviewTab::class,
            },
        ])->layout('layouts.learning');
    }
}
