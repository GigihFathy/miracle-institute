<?php

namespace App\Livewire\Courses;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\TopicProgress;
use App\Services\CourseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CourseShow extends Component
{
    use AuthorizesRequests;

    public Course $course;
    public bool $enrolled = false;

    public ?Certificate $courseCertificate = null;
    public array $topicCertificates = [];
    public array $topicStatusMap;

    public function mount(string $slug)
    {
        $this->course = Course::with([
            'studyProgram',
            'topics.materials',
            'topics.sessions',
            'topics.assessments',
        ])->where('slug', $slug)->firstOrFail();

        $enrollment = auth()->user()
            ->courseEnrollments()
            ->where('course_id', $this->course->id)
            ->first();

        $this->enrolled = (bool) $enrollment;

        $this->courseCertificate = Certificate::where('user_id', auth()->id())
            ->where('type', 'course')
            ->where('course_id', $this->course->id)
            ->latest()
            ->first();

        $this->topicStatusMap = [];

        if ($enrollment) {
            $this->topicStatusMap = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->pluck('status', 'topic_id')
                ->toArray();
        }

        $this->topicCertificates = Certificate::where('user_id', auth()->id())
            ->where('type', 'topic')
            ->where('course_id', $this->course->id)
            ->get()
            ->keyBy('topic_id')
            ->toArray();
    }

    public function enroll(CourseService $courseService)
    {
        $this->authorize('enroll', $this->course);

        try {
            $courseService->enrollUser(auth()->id(), $this->course->id);
            $this->enrolled = true;
            session()->flash('success', 'Course berhasil diikuti.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.courses.course-show')->layout('layouts.learning');
    }
}