<?php

namespace App\Livewire\Dashboard;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\LearningSession;
use App\Models\TopicProgress;
use App\Services\ProgressService;
use Livewire\Component;

class StudentDashboard extends Component
{
    public function render(ProgressService $progressService)
    {
        $user = auth()->user();
        $summary = $progressService->getUserSummary($user);

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
            });

        $courseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->all();

        $upcomingSessions = LearningSession::with('topic.course')
            ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->where('start_at', '>=', now()->subHours(12))
            ->orderBy('start_at')
            ->take(5)
            ->get();

        $latestCertificates = Certificate::where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.dashboard.student-dashboard', compact(
            'summary',
            'enrollments',
            'upcomingSessions',
            'latestCertificates'
        ))->layout('layouts.learning');
    }
}