<?php

namespace App\Livewire\Disciples\Dashboard;

use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\VideoSession;
use Livewire\Component;

class DashboardIndex extends Component
{
    public function render()
    {
        $user = auth()->user();
        $userId = $user->id;

        $roles = $user->roles->pluck('name')->all();
        $hasStudentRole = in_array('student', $roles, true);

        $mentoredTopics = Topic::with([
                'course.studyProgram',
                'course.assessment',
            ])
            ->withCount(['materials', 'videoSessions'])
            ->where('teacher_id', $userId)
            ->latest()
            ->get();

        $mentoredTopicIds = $mentoredTopics->pluck('id');
        $mentoredCourseIds = $mentoredTopics->pluck('course_id')->unique()->values();

        $mentoredTopicsByCourse = $mentoredTopics
            ->groupBy('course_id')
            ->values()
            ->map(function ($courseTopics) {
                $course = $courseTopics->first()?->course;
                $totalTopics = $courseTopics->count();
                $totalMaterials = (int) $courseTopics->sum('materials_count');
                $totalSessions = (int) $courseTopics->sum('video_sessions_count');

                $topicsWithoutMaterials = $courseTopics->where('materials_count', 0)->count();
                $topicsWithoutSessions = $courseTopics->where('video_sessions_count', 0)->count();

                $assessment = $course?->assessment;
                $lastUpdatedAt = $courseTopics->sortByDesc('updated_at')->first()?->updated_at;

                $healthPct = 0;
                if ($totalTopics > 0) {
                    $readyTopics = $courseTopics->filter(function ($topic) {
                        return $topic->materials_count > 0 && $topic->video_sessions_count > 0;
                    })->count();

                    $healthPct = (int) round(($readyTopics / $totalTopics) * 100);
                }

                return (object) [
                    'course' => $course,
                    'topics' => $courseTopics->sortBy('sort_order')->values(),
                    'totalTopics' => $totalTopics,
                    'totalMaterials' => $totalMaterials,
                    'totalSessions' => $totalSessions,
                    'topicsWithoutMaterials' => $topicsWithoutMaterials,
                    'topicsWithoutSessions' => $topicsWithoutSessions,
                    'assessment' => $assessment,
                    'healthPct' => $healthPct,
                    'lastUpdatedAt' => $lastUpdatedAt,
                ];
            })
            ->sortByDesc(fn ($item) => $item->lastUpdatedAt?->timestamp ?? 0)
            ->values();

        $mentorStudentsCount = TopicProgress::whereIn('topic_id', $mentoredTopicIds)
            ->distinct('course_enrollment_id')
            ->count('course_enrollment_id');

        $mentorCoursesCount = $mentoredCourseIds->count();

        $mentorMaterialsCount = Material::where('uploader_id', $userId)->count();

        $mentorSessionsCount = VideoSession::whereHas('topic', function ($q) use ($userId) {
            $q->where('teacher_id', $userId);
        })->count();

        $mentorAssessmentsCount = Assessment::whereIn('course_id', $mentoredCourseIds)->count();

        $studioHealthPct = $mentoredTopics->count() > 0
            ? (int) round(
                $mentoredTopics
                    ->filter(fn ($topic) => $topic->materials_count > 0 && $topic->video_sessions_count > 0)
                    ->count() / $mentoredTopics->count() * 100
            )
            : 0;

        $attentionTopics = $mentoredTopics
            ->filter(fn ($topic) => $topic->materials_count === 0 || $topic->video_sessions_count === 0)
            ->take(6);

        $latestMaterials = Material::with(['topic.course', 'uploader'])
            ->where('uploader_id', $userId)
            ->latest()
            ->take(6)
            ->get();

        $upcomingSessions = VideoSession::with(['topic.course'])
            ->whereHas('topic', function ($q) use ($userId) {
                $q->where('teacher_id', $userId);
            })
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->take(6)
            ->get();

        $latestAssessments = Assessment::with('course.studyProgram')
            ->whereIn('course_id', $mentoredCourseIds)
            ->latest()
            ->take(6)
            ->get();

        $studentSnapshot = [
            'courses' => 0,
            'topics_completed' => 0,
            'certificates' => 0,
            'progress_pct' => 0,
            'next_session' => null,
        ];

        if ($hasStudentRole) {
            $myEnrollmentIds = CourseEnrollment::where('user_id', $userId)->pluck('id');

            $studentSnapshot['courses'] = $myEnrollmentIds->count();
            $studentSnapshot['topics_completed'] = TopicProgress::whereIn('course_enrollment_id', $myEnrollmentIds)
                ->where('status', 'completed')
                ->count();
            $studentSnapshot['certificates'] = Certificate::where('user_id', $userId)->count();

            $studentTotalTopics = TopicProgress::whereIn('course_enrollment_id', $myEnrollmentIds)->distinct('topic_id')->count('topic_id');
            $studentSnapshot['progress_pct'] = $studentTotalTopics > 0
                ? (int) round(($studentSnapshot['topics_completed'] / $studentTotalTopics) * 100)
                : 0;

            $studentSnapshot['next_session'] = VideoSession::with('topic.course')
                ->whereHas('topic', fn ($q) => $q->whereIn('course_id', $mentoredCourseIds))
                ->where('start_at', '>=', now())
                ->orderBy('start_at')
                ->first();
        }

        return view('livewire.disciples.dashboard.index', [
            'mentorTopicsCount' => $mentoredTopics->count(),
            'mentorCoursesCount' => $mentorCoursesCount,
            'mentorMaterialsCount' => $mentorMaterialsCount,
            'mentorStudentsCount' => $mentorStudentsCount,
            'mentorSessionsCount' => $mentorSessionsCount,
            'mentorAssessmentsCount' => $mentorAssessmentsCount,
            'studioHealthPct' => $studioHealthPct,
            'mentoredTopicsByCourse' => $mentoredTopicsByCourse,
            'attentionTopics' => $attentionTopics,
            'latestMaterials' => $latestMaterials,
            'upcomingSessions' => $upcomingSessions,
            'latestAssessments' => $latestAssessments,
            'studentSnapshot' => $studentSnapshot,
            'hasStudentRole' => $hasStudentRole,
        ])->layout('layouts.learning');
    }
}