<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\WithTableState;
use App\Models\CourseEnrollment;
use App\Models\VideoSession;
use App\Models\TopicProgress;
use App\Models\Certificate;
use App\Services\ProgressService;
use Livewire\Component;

class MyLearning extends Component
{
    use WithTableState;

    public $tab = 'courses';
    public $searchCourse = '';
    public $filterCourse = 'all';
    public $searchCertificate = '';
    public $sortCertificate = 'latest';

    public function resetCourseFilters()
    {
        $this->searchCourse = '';
        $this->filterCourse = 'all';
    }

    public function resetCertificateFilters()
    {
        $this->searchCertificate = '';
        $this->sortCertificate = 'latest';
    }

    public function updatedSearchCourse()
    {
        // optional: normalize search input
        $this->searchCourse = trim($this->searchCourse);
    }

    public function updatedSearchCertificate()
    {
        $this->searchCertificate = trim($this->searchCertificate);
    }

    public function render(ProgressService $progressService)
    {
        $user = auth()->user();
        $summary = array_merge([
            'courses_enrolled' => 0,
            'certificates' => 0,
        ], (array) $progressService->getUserSummary($user));

        $hasEnrollments = CourseEnrollment::where('user_id', $user->id)->exists();

        $enrollments = CourseEnrollment::with(['course.studyProgram', 'course.topics'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($enrollment) {
                $totalTopics = $enrollment->course?->topics?->count() ?? 0;
                $completedTopics = TopicProgress::where('course_enrollment_id', $enrollment->id)
                    ->where('status', 'completed')
                    ->count();

                return [
                    'enrollment' => $enrollment,
                    'totalTopics' => $totalTopics,
                    'completedTopics' => $completedTopics,
                    'percent' => $totalTopics > 0 ? (int) round(($completedTopics / $totalTopics) * 100) : 0,
                ];
            })
            ->filter(function ($row) {
                // search by course title
                if (!empty($this->searchCourse)) {
                    $title = $row['enrollment']->course?->title ?? '';
                    if (stripos($title, $this->searchCourse) === false) {
                        return false;
                    }
                }

                // filter by completion status
                if ($this->filterCourse === 'completed' && ($row['percent'] < 100)) {
                    return false;
                }

                if ($this->filterCourse === 'in_progress' && ($row['percent'] >= 100)) {
                    return false;
                }

                return true;
            })->values();

        $courseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->all();

        $upcomingSessions = VideoSession::with('topic.course')
            ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->where('start_at', '>=', now()->subHours(12))
            ->orderBy('start_at')
            ->take(5)
            ->get();

        $certificates = Certificate::with(['course', 'user.courseEnrollments.course'])
            ->where('user_id', $user->id)
            ->when(filled($this->searchCertificate), function ($query) {
                $query->where(function ($certificateQuery) {
                    $certificateQuery
                        ->where('certificate_number', 'like', '%' . $this->searchCertificate . '%')
                        ->orWhereHas('course', function ($courseQuery) {
                            $courseQuery->where('title', 'like', '%' . $this->searchCertificate . '%');
                        });
                });
            })
            ->orderBy('issued_at', $this->sortCertificate === 'oldest' ? 'asc' : 'desc')
            ->take(6)
            ->get();

        return view('livewire.dashboard.my-learning', compact(
            'summary',
            'hasEnrollments',
            'enrollments',
            'upcomingSessions',
            'certificates'
        ))->layout('layouts.learning');
    }
}
