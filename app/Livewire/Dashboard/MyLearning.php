<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\WithTableState;
use App\Models\CourseEnrollment;
use App\Models\VideoSession;
use App\Models\TopicProgress;
use App\Services\ProgressService;
use Livewire\Component;

class MyLearning extends Component
{
    use WithTableState;

    public function render(ProgressService $progressService)
    {
        $user = auth()->user();
        $summary = array_merge([
            'courses_enrolled' => 0,
            'topics_completed' => 0,
            'certificates' => 0,
        ], (array) $progressService->getUserSummary($user));

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

        $upcomingSessions = VideoSession::with('topic.course')
            ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->where('start_at', '>=', now()->subHours(12))
            ->orderBy('start_at')
            ->take(5)
            ->get();

        return view('livewire.dashboard.my-learning', compact(
            'summary',
            'enrollments',
            'upcomingSessions'
        ))->layout('layouts.learning');
    }
}