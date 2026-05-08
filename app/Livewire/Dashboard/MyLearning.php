<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\WithTableState;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Models\Topic;
use App\Models\VideoSession;
use App\Services\ProgressService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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

        $enrollments = CourseEnrollment::with([
            'course.studyProgram',
            'course.topics' => fn ($q) => $q->orderBy('sort_order')->orderBy('name'),
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $enrollmentIds = $enrollments->pluck('id')->all();

        $progressRowsByEnrollment = TopicProgress::whereIn('course_enrollment_id', $enrollmentIds)
            ->with(['topic.course'])
            ->get()
            ->groupBy('course_enrollment_id');

        $enrollmentCards = $enrollments->map(function ($enrollment) use ($progressRowsByEnrollment) {
            $progressRows = $progressRowsByEnrollment->get($enrollment->id, collect());

            $completedTopicIds = $progressRows
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->all();

            $completedTopics = count($completedTopicIds);
            $inProgressTopics = $progressRows->where('status', 'in_progress')->count();
            $totalTopics = $enrollment->course?->topics?->count() ?? 0;

            $percent = $totalTopics > 0
                ? (int) round(($completedTopics / $totalTopics) * 100)
                : 0;

            $nextTopic = $enrollment->course?->topics?->first(function ($topic) use ($completedTopicIds) {
                return ! in_array($topic->id, $completedTopicIds, true);
            });

            $lastActivityAt = $progressRows->sortByDesc('updated_at')->first()?->updated_at;

            $statusLabel = match (true) {
                $totalTopics === 0 => 'No topics',
                $percent === 100 => 'Completed',
                $percent >= 75 => 'Near completion',
                $percent > 0 => 'In progress',
                default => 'Not started',
            };

            return [
                'enrollment' => $enrollment,
                'totalTopics' => $totalTopics,
                'completedTopics' => $completedTopics,
                'inProgressTopics' => $inProgressTopics,
                'percent' => $percent,
                'statusLabel' => $statusLabel,
                'lastActivityAt' => $lastActivityAt,
                'nextTopic' => $nextTopic,
                'continueUrl' => $nextTopic
                    ? route('topics.show', $nextTopic->slug)
                    : route('courses.show', $enrollment->course?->slug),
            ];
        });

        $totalTopicsAcrossEnrollments = (int) $enrollmentCards->sum('totalTopics');
        $completedTopicsAcrossEnrollments = (int) $enrollmentCards->sum('completedTopics');

        $overallProgress = $totalTopicsAcrossEnrollments > 0
            ? (int) round(($completedTopicsAcrossEnrollments / $totalTopicsAcrossEnrollments) * 100)
            : 0;

        $recommendedCourse = $enrollmentCards
            ->filter(fn ($row) => $row['percent'] < 100)
            ->sortByDesc(fn ($row) => optional($row['lastActivityAt'])->timestamp ?? 0)
            ->first();

        if (! $recommendedCourse) {
            $recommendedCourse = $enrollmentCards
                ->sortByDesc(fn ($row) => optional($row['lastActivityAt'])->timestamp ?? 0)
                ->first();
        }

        $courseIds = $enrollments->pluck('course_id')->all();

        $upcomingSessions = VideoSession::with('topic.course')
            ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->whereBetween('start_at', [now(), now()->addDays(14)])
            ->orderBy('start_at')
            ->take(6)
            ->get();

        $latestCertificates = Certificate::where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get();

        $recentTopicCompletions = TopicProgress::with(['topic.course'])
            ->whereIn('course_enrollment_id', $enrollmentIds)
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $activityFeed = collect();

        foreach ($recentTopicCompletions as $progress) {
            $activityFeed->push([
                'type' => 'topic',
                'title' => 'Completed topic',
                'subtitle' => $progress->topic?->course?->title . ' · ' . $progress->topic?->name,
                'time' => $progress->updated_at,
                'tone' => 'emerald',
                'link' => route('topics.show', $progress->topic?->slug),
            ]);
        }

        foreach ($upcomingSessions as $session) {
            $activityFeed->push([
                'type' => 'session',
                'title' => 'Upcoming session',
                'subtitle' => $session->topic?->course?->title . ' · ' . $session->topic?->name,
                'time' => $session->start_at,
                'tone' => 'blue',
                'link' => route('topics.show', $session->topic?->slug),
            ]);
        }

        foreach ($latestCertificates as $certificate) {
            $activityFeed->push([
                'type' => 'certificate',
                'title' => 'Certificate issued',
                'subtitle' => $certificate->certificate_number . ' · ' . ucfirst($certificate->type),
                'time' => $certificate->created_at,
                'tone' => 'amber',
                'link' => route('certificates.download', $certificate->id),
            ]);
        }

        $activityFeed = $activityFeed
            ->sortByDesc(fn ($item) => optional($item['time'])->timestamp ?? 0)
            ->values()
            ->take(8);

        $todaySessionsCount = $upcomingSessions->filter(fn ($session) => $session->start_at?->isToday())->count();
        $nextSession = $upcomingSessions->first();

        return view('livewire.dashboard.my-learning', [
            'summary' => $summary,
            'enrollmentCards' => $enrollmentCards,
            'overallProgress' => $overallProgress,
            'recommendedCourse' => $recommendedCourse,
            'upcomingSessions' => $upcomingSessions,
            'latestCertificates' => $latestCertificates,
            'activityFeed' => $activityFeed,
            'todaySessionsCount' => $todaySessionsCount,
            'nextSession' => $nextSession,
            'nowLabel' => now()->format('l, d M Y · H:i'),
        ])->layout('layouts.learning');
    }
}