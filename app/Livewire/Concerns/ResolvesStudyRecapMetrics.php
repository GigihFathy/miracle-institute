<?php

namespace App\Livewire\Concerns;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\VideoSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ResolvesStudyRecapMetrics
{
    private function mentorCourseIds(): Collection
    {
        return Topic::query()
            ->where('teacher_id', auth()->id())
            ->whereNotNull('course_id')
            ->distinct()
            ->pluck('course_id')
            ->values();
    }

    private function buildCourseSummary(Course $course): array
    {
        $topics = $course->topics->values();
        $enrollments = $course->enrollments->values();

        $studentIds = $enrollments->pluck('user_id')->unique()->values();
        $enrollmentIds = $enrollments->pluck('id')->values();
        $topicIds = $topics->pluck('id')->values();
        $materialIds = $topics->flatMap(fn ($topic) => $topic->materials?->pluck('id') ?? collect())->values();
        $sessionIds = $topics->flatMap(fn ($topic) => $topic->videoSessions?->pluck('id') ?? collect())->values();

        $topicPossible = $enrollments->count() && $topics->count()
            ? $enrollments->count() * $topics->count()
            : 0;

        $materialPossible = $studentIds->count() && $materialIds->count()
            ? $studentIds->count() * $materialIds->count()
            : 0;

        $sessionPossible = $studentIds->count() && $sessionIds->count()
            ? $studentIds->count() * $sessionIds->count()
            : 0;

        $completedTopicRecords = TopicProgress::whereIn('course_enrollment_id', $enrollmentIds)
            ->whereIn('topic_id', $topicIds)
            ->where('status', 'completed')
            ->count();

        $completedMaterialRecords = MaterialProgress::whereIn('user_id', $studentIds)
            ->whereIn('material_id', $materialIds)
            ->where('status', 'completed')
            ->count();

        $viewedMaterialRecords = MaterialProgress::whereIn('user_id', $studentIds)
            ->whereIn('material_id', $materialIds)
            ->whereIn('status', ['viewed', 'completed'])
            ->count();

        $attendanceAttendedRecords = Attendance::whereIn('user_id', $studentIds)
            ->whereIn('video_session_id', $sessionIds)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $assessmentIds = collect([$course->assessment?->id])->filter()->values();

        $assessmentAttemptsQuery = AssessmentAttempt::query()
            ->whereIn('assessment_id', $assessmentIds)
            ->whereIn('user_id', $studentIds)
            ->whereNotNull('submitted_at');

        $assessmentAttemptsCount = $assessmentIds->isNotEmpty()
            ? (clone $assessmentAttemptsQuery)->count()
            : 0;

        $assessmentAvg = $assessmentIds->isNotEmpty() && $assessmentAttemptsCount
            ? (float) (clone $assessmentAttemptsQuery)->avg('score')
            : null;

        $assessmentPassedRate = $assessmentIds->isNotEmpty() && $assessmentAttemptsCount
            ? round(((clone $assessmentAttemptsQuery)->where('passed', true)->count() / $assessmentAttemptsCount) * 100, 1)
            : null;

        return [
            'topic_completion_rate' => $topicPossible
                ? round(($completedTopicRecords / $topicPossible) * 100, 1)
                : 0,
            'material_completion_rate' => $materialPossible
                ? round(($completedMaterialRecords / $materialPossible) * 100, 1)
                : 0,
            'material_view_rate' => $materialPossible
                ? round(($viewedMaterialRecords / $materialPossible) * 100, 1)
                : 0,
            'attendance_rate' => $sessionPossible
                ? round(($attendanceAttendedRecords / $sessionPossible) * 100, 1)
                : 0,
            'assessment_avg_score' => $assessmentAvg !== null
                ? round($assessmentAvg, 1)
                : null,
            'assessment_passed_rate' => $assessmentPassedRate,
            'students_count' => $studentIds->count(),
            'topics_count' => $topics->count(),
            'materials_count' => $materialIds->count(),
            'sessions_count' => $sessionIds->count(),
            'assessment_status' => $course->assessment ? ucfirst($course->assessment->status) : 'No Assessment',
        ];
    }

    private function decorateCourseRow(Course $course): Course
    {
        $summary = $this->buildCourseSummary($course);

        $course->setAttribute('topic_completion_rate', $summary['topic_completion_rate']);
        $course->setAttribute('material_completion_rate', $summary['material_completion_rate']);
        $course->setAttribute('material_view_rate', $summary['material_view_rate']);
        $course->setAttribute('attendance_rate', $summary['attendance_rate']);
        $course->setAttribute('assessment_avg_score', $summary['assessment_avg_score']);
        $course->setAttribute('assessment_passed_rate', $summary['assessment_passed_rate']);
        $course->setAttribute('assessment_status', $summary['assessment_status']);
        $course->setAttribute('assessment_label', $summary['assessment_status']);
        $course->setAttribute('students_count', $summary['students_count']);
        $course->setAttribute('topics_count_live', $summary['topics_count']);
        $course->setAttribute('materials_count_live', $summary['materials_count']);
        $course->setAttribute('sessions_count_live', $summary['sessions_count']);
        $course->setAttribute('completion_rate', $summary['topic_completion_rate']);

        return $course;
    }

    private function buildTopicRows(Course $course): Collection
    {
        $enrollments = $course->enrollments->values();
        $studentIds = $enrollments->pluck('user_id')->unique()->values();
        $enrollmentIds = $enrollments->pluck('id')->values();

        return $course->topics->map(function ($topic) use ($enrollmentIds, $studentIds, $course) {
            $materialIds = $topic->materials?->pluck('id')->values() ?? collect();
            $sessionIds = $topic->videoSessions?->pluck('id')->values() ?? collect();

            $topicProgressCompleted = TopicProgress::whereIn('course_enrollment_id', $enrollmentIds)
                ->where('topic_id', $topic->id)
                ->where('status', 'completed')
                ->count();

            $materialCompleted = MaterialProgress::whereIn('user_id', $studentIds)
                ->whereIn('material_id', $materialIds)
                ->where('status', 'completed')
                ->count();

            $attendanceAttended = Attendance::whereIn('user_id', $studentIds)
                ->whereIn('video_session_id', $sessionIds)
                ->whereIn('status', ['present', 'late'])
                ->count();

            $topicCompletionRate = $enrollmentIds->count()
                ? round(($topicProgressCompleted / $enrollmentIds->count()) * 100, 1)
                : 0;

            $materialCompletionRate = $studentIds->count() && $materialIds->count()
                ? round(($materialCompleted / ($studentIds->count() * $materialIds->count())) * 100, 1)
                : 0;

            $attendanceRate = $studentIds->count() && $sessionIds->count()
                ? round(($attendanceAttended / ($studentIds->count() * $sessionIds->count())) * 100, 1)
                : 0;

            return [
                'id' => $topic->id,
                'name' => $topic->name,
                'slug' => $topic->slug,
                'description' => $topic->description,
                'materials_count' => $materialIds->count(),
                'sessions_count' => $sessionIds->count(),
                'completed_students' => $topicProgressCompleted,
                'topic_completion_rate' => $topicCompletionRate,
                'material_completion_rate' => $materialCompletionRate,
                'attendance_rate' => $attendanceRate,
                'course_assessment_status' => $course->assessment ? 'Available' : 'Unavailable',
            ];
        })->sortByDesc('topic_completion_rate')->values();
    }

    private function buildStudentRows(Course $course): Collection
    {
        $topics = $course->topics->values();
        $topicIds = $topics->pluck('id')->values();
        $materialIds = $topics->flatMap(fn ($topic) => $topic->materials?->pluck('id') ?? collect())->values();
        $sessionIds = $topics->flatMap(fn ($topic) => $topic->videoSessions?->pluck('id') ?? collect())->values();
        $assessmentId = $course->assessment?->id;

        return $course->enrollments->map(function ($enrollment) use ($topicIds, $materialIds, $sessionIds, $assessmentId, $course) {
            $completedTopics = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->whereIn('topic_id', $topicIds)
                ->where('status', 'completed')
                ->count();

            $completedMaterials = MaterialProgress::where('user_id', $enrollment->user_id)
                ->whereIn('material_id', $materialIds)
                ->where('status', 'completed')
                ->count();

            $attendedSessions = Attendance::where('user_id', $enrollment->user_id)
                ->whereIn('video_session_id', $sessionIds)
                ->whereIn('status', ['present', 'late'])
                ->count();

            $assessmentQuery = $assessmentId
                ? AssessmentAttempt::query()
                    ->where('assessment_id', $assessmentId)
                    ->where('user_id', $enrollment->user_id)
                    ->whereNotNull('submitted_at')
                : null;

            $averageScore = $assessmentQuery
                ? (clone $assessmentQuery)->avg('score')
                : null;

            $topicCompletionRate = $topicIds->count()
                ? round(($completedTopics / $topicIds->count()) * 100, 1)
                : 0;

            $materialCompletionRate = $materialIds->count()
                ? round(($completedMaterials / $materialIds->count()) * 100, 1)
                : 0;

            $attendanceRate = $sessionIds->count()
                ? round(($attendedSessions / $sessionIds->count()) * 100, 1)
                : 0;

            return [
                'student_id' => $enrollment->user_id,
                'student_name' => $enrollment->user?->full_name ?? $enrollment->user?->name ?? 'Student',
                'student_email' => $enrollment->user?->email,
                'topic_completion_rate' => $topicCompletionRate,
                'material_completion_rate' => $materialCompletionRate,
                'attendance_rate' => $attendanceRate,
                'assessment_avg_score' => $averageScore !== null ? round((float) $averageScore, 1) : null,
                'assessment_status' => $course->assessment ? 'Available' : 'Unavailable',
            ];
        })->sortByDesc(fn ($row) => $row['topic_completion_rate'])->values();
    }

    private function buildOverallSummary(Collection $courseIds): array
    {
        $topics = Topic::whereIn('course_id', $courseIds)->get();
        $courseEnrollments = CourseEnrollment::whereIn('course_id', $courseIds)->get();
        $students = $courseEnrollments->pluck('user_id')->unique()->values();

        $topicIds = $topics->pluck('id')->values();
        $materialIds = Material::whereIn('topic_id', $topicIds)->pluck('id')->values();
        $sessionIds = VideoSession::whereIn('topic_id', $topicIds)->pluck('id')->values();
        $assessmentIds = Assessment::whereIn('course_id', $courseIds)->pluck('id')->values();

        $topicPossible = $courseEnrollments->count() && $topicIds->count()
            ? $courseEnrollments->count() * $topicIds->count()
            : 0;

        $materialPossible = $students->count() && $materialIds->count()
            ? $students->count() * $materialIds->count()
            : 0;

        $attendancePossible = $students->count() && $sessionIds->count()
            ? $students->count() * $sessionIds->count()
            : 0;

        $completedTopics = TopicProgress::whereIn('course_enrollment_id', $courseEnrollments->pluck('id'))
            ->whereIn('topic_id', $topicIds)
            ->where('status', 'completed')
            ->count();

        $completedMaterials = MaterialProgress::whereIn('user_id', $students)
            ->whereIn('material_id', $materialIds)
            ->where('status', 'completed')
            ->count();

        $attendanceCount = Attendance::whereIn('user_id', $students)
            ->whereIn('video_session_id', $sessionIds)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $assessmentAttemptsQuery = AssessmentAttempt::whereIn('assessment_id', $assessmentIds)
            ->whereIn('user_id', $students)
            ->whereNotNull('submitted_at');

        $assessmentAttemptsCount = $assessmentIds->isNotEmpty()
            ? (clone $assessmentAttemptsQuery)->count()
            : 0;

        $assessmentAvg = $assessmentIds->isNotEmpty() && $assessmentAttemptsCount
            ? (float) (clone $assessmentAttemptsQuery)->avg('score')
            : null;

        return [
            'courses_count' => $courseIds->count(),
            'topics_count' => $topics->count(),
            'students_count' => $students->count(),
            'topic_completion_rate' => $topicPossible ? round(($completedTopics / $topicPossible) * 100, 1) : 0,
            'material_completion_rate' => $materialPossible ? round(($completedMaterials / $materialPossible) * 100, 1) : 0,
            'attendance_rate' => $attendancePossible ? round(($attendanceCount / $attendancePossible) * 100, 1) : 0,
            'assessment_avg_score' => $assessmentAvg !== null ? round($assessmentAvg, 1) : null,
            'courses_with_assessment' => Assessment::whereIn('course_id', $courseIds)->count(),
        ];
    }
}