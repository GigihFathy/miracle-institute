<?php

namespace App\Livewire\Disciples\StudyRecap;

use App\Livewire\Concerns\ResolvesStudyRecapMetrics;
use App\Models\Course;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopicRecap extends Component
{
    use ResolvesStudyRecapMetrics;

    public Course $course;

    public function mount(Course $course): void
    {
        if (session('active_role') !== 'disciples') {
            abort(403);
        }

        $courseIds = $this->mentorCourseIds();

        if (! $courseIds->contains($course->id)) {
            abort(403);
        }

        $this->course = $course->load(['studyProgram', 'assessment', 'topics.materials', 'topics.videoSessions', 'enrollments.user']);
    }

    public function render()
    {
        $course = $this->decorateCourseRow($this->course);
        $summary = $this->buildCourseSummary($course);
        $topicRows = $this->buildTopicRows($course);

        return view('livewire.disciples.study-recap.topic-recap', [
            'course' => $course,
            'summary' => $summary,
            'topicRows' => $topicRows,
        ])->layout('layouts.learning');
    }
}