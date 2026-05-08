<?php

namespace App\Livewire\Disciples\StudyRecap;

use App\Livewire\Concerns\ResolvesStudyRecapMetrics;
use App\Models\Course;
use Livewire\Component;

class StudentRecap extends Component
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
        $studentRows = $this->buildStudentRows($course);

        return view('livewire.disciples.study-recap.student-recap', [
            'course' => $course,
            'summary' => $summary,
            'studentRows' => $studentRows,
        ])->layout('layouts.learning');
    }
}