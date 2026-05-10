<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\CourseEnrollment;
use App\Models\Topic;
use App\Models\TopicProgress;
use Livewire\Component;

class StudentsTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public bool $canManageStudents = false;

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
        $enrollments = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $this->topic->course_id)
            ->get();

        $progressMap = TopicProgress::query()
            ->where('topic_id', $this->topic->id)
            ->whereIn('course_enrollment_id', $enrollments->pluck('id'))
            ->get()
            ->keyBy('course_enrollment_id');

        $students = $enrollments->map(function ($enrollment) use ($progressMap) {
            $progress = $progressMap->get($enrollment->id);

            $status = $progress->status ?? 'not_started';

            $percent = match ($status) {
                'completed' => 100,
                'in_progress' => 50,
                default => 0,
            };

            return [
                'enrollment' => $enrollment,
                'progress' => $progress,
                'status' => $status,
                'percent' => $percent,
            ];
        });

        return view('livewire.mentor.topics.tabs.students-tab', [
            'students' => $students,
        ]);
    }
}