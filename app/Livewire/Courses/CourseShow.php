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

    public bool $isGuest = true;

    public function mount(string $slug)
    {
        $this->course = Course::with([
            'studyProgram',
            'topics' => fn ($q) => $q->withCount(['materials', 'sessions']),
        ])->where('slug', $slug)->firstOrFail();

        if (!auth()->check()) {
            $this->enrolled = false;
            $this->topicStatusMap = [];
            $this->topicCertificates = [];
            return;
        }
        
        $user = auth()->user();

        if (!$user) {
            $this->isGuest = true;
            $this->enrolled = false;
            $this->courseCertificate = null;
            $this->topicStatusMap = [];
            $this->topicCertificates = [];
            return;
        }

        $this->isGuest = false;

        $enrollment = $user->courseEnrollments()
            ->where('course_id', $this->course->id)
            ->first();

        $this->enrolled = (bool) $enrollment;

        if (!$this->enrolled) {
            return;
        }

        $this->course->load([
            'topics.materials',
            'topics.sessions',
            'topics.assessments',
        ]);

        $this->courseCertificate = Certificate::where('user_id', $user->id)
            ->where('type', 'course')
            ->where('course_id', $this->course->id)
            ->latest()
            ->first();

        $this->topicStatusMap = TopicProgress::where('course_enrollment_id', $enrollment->id)
            ->pluck('status', 'topic_id')
            ->toArray();

        $this->topicCertificates = Certificate::where('user_id', $user->id)
            ->where('type', 'topic')
            ->where('course_id', $this->course->id)
            ->get()
            ->keyBy('topic_id')
            ->toArray();
    }

    public function enroll(CourseService $courseService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

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