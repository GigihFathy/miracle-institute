<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\User;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgressService
{
    public function markMaterialViewed(string $userId, string $materialId): MaterialProgress
    {
        $material = Material::query()
            ->with(['topic'])
            ->findOrFail($materialId);

        $this->requireEnrollment($userId, $material->topic->course_id);

        return DB::transaction(function () use ($userId, $material) {
            $progress = MaterialProgress::query()->firstOrNew([
                'user_id' => $userId,
                'material_id' => $material->id,
            ]);

            $progress->status = 'in_progress';
            $progress->started_at ??= now();
            $progress->save();

            return $progress;
        });
    }

    public function markMaterialCompleted(string $userId, string $materialId): array
    {
        $material = Material::query()
            ->with(['topic.materials', 'topic.videoSessions'])
            ->findOrFail($materialId);

        $enrollment = $this->requireEnrollment($userId, $material->topic->course_id);

        return DB::transaction(function () use ($userId, $material, $enrollment) {
            $materialProgress = MaterialProgress::query()->firstOrNew([
                'user_id' => $userId,
                'material_id' => $material->id,
            ]);

            $materialProgress->status = 'completed';
            $materialProgress->started_at ??= now();
            $materialProgress->completed_at = now();
            $materialProgress->save();

            $snapshot = $this->topicCompletionSnapshot($userId, $material->topic_id);

            $topicProgress = TopicProgress::query()->firstOrNew([
                'course_enrollment_id' => $enrollment->id,
                'topic_id' => $material->topic_id,
            ]);

            $topicProgress->status = $snapshot['can_complete'] ? 'completed' : 'in_progress';
            $topicProgress->started_at ??= now();

            if ($snapshot['can_complete']) {
                $topicProgress->completed_at = now();
            } else {
                $topicProgress->completed_at = null;
            }

            $topicProgress->save();

            $this->syncCertificateState($userId, $material->topic->course_id);

            return [
                'material_progress' => $materialProgress,
                'topic_progress' => $topicProgress,
                'snapshot' => $snapshot,
            ];
        });
    }

    public function topicCompletionSnapshot(string $userId, string $topicId): array
    {
        $topic = Topic::query()
            ->with([
                'materials:id,topic_id,name,sort_order',
                'videoSessions:id,topic_id,title,start_at,end_at,status',
            ])
            ->findOrFail($topicId);

        $this->requireEnrollment($userId, $topic->course_id);

        $this->syncEndedSessionAbsences($userId, $topic);

        $materialIds = $topic->materials->pluck('id')->all();

        $materialProgresses = MaterialProgress::query()
            ->where('user_id', $userId)
            ->whereIn('material_id', $materialIds)
            ->get()
            ->keyBy('material_id');

        $completedMaterials = $topic->materials->filter(function ($material) use ($materialProgresses) {
            return ($materialProgresses[$material->id]->status ?? null) === 'completed';
        });

        $incompleteMaterials = $topic->materials->filter(function ($material) use ($materialProgresses) {
            return ($materialProgresses[$material->id]->status ?? null) !== 'completed';
        });

        $qualifyingSessions = $topic->videoSessions->filter(function ($session) {
            return $session->status !== 'cancelled';
        });

        $sessionIds = $qualifyingSessions->pluck('id')->all();

        $attendances = Attendance::query()
            ->where('user_id', $userId)
            ->whereIn('video_session_id', $sessionIds)
            ->get()
            ->keyBy('video_session_id');

        $satisfiedSessionIds = [];

        foreach ($qualifyingSessions as $session) {
            $attendance = $attendances->get($session->id);
            $sessionEnded = $this->sessionHasEnded($session);

            $isSatisfied = false;

            if ($attendance && in_array($attendance->status, ['present', 'late'], true)) {
                $isSatisfied = true;
            } elseif ($sessionEnded && $attendance && in_array($attendance->status, ['online', 'absent'], true)) {
                $isSatisfied = true;
            } elseif ($sessionEnded &&!$attendance) {
                $isSatisfied = true;
            }

            if ($isSatisfied) {
                $satisfiedSessionIds[] = $session->id;
            }
        }

        $missingSessions = $qualifyingSessions
            ->reject(fn ($session) => in_array($session->id, $satisfiedSessionIds, true))
            ->pluck('title')
            ->values()
            ->all();

        $allMaterialsCompleted = $topic->materials->isEmpty()
            ? true
            : $completedMaterials->count() === $topic->materials->count();

        $allSessionsRequirementMet = $qualifyingSessions->isEmpty()
            ? true
            : count($satisfiedSessionIds) === $qualifyingSessions->count();

        $allSessionsClosed = $qualifyingSessions->isEmpty()
            ? true
            : $qualifyingSessions->every(fn ($session) => $this->sessionHasEnded($session));

        $reasons = [];

        if (!$allMaterialsCompleted) {
            $reasons[] = 'Semua materi harus selesai terlebih dahulu.';
        }

        if (!$allSessionsClosed) {
            $reasons[] = 'Masih ada sesi video yang belum berakhir.';
        }

        if (!$allSessionsRequirementMet) {
            $reasons[] = 'Masih ada syarat sesi yang belum terpenuhi.';
        }

        return [
            'total_materials' => $topic->materials->count(),
            'completed_materials' => $completedMaterials->count(),
            'incomplete_materials' => $incompleteMaterials->pluck('name')->values()->all(),
            'all_materials_completed' => $allMaterialsCompleted,
            'total_sessions' => $qualifyingSessions->count(),
            'attended_sessions' => count($satisfiedSessionIds),
            'missing_sessions' => $missingSessions,
            'all_sessions_attended' => $allSessionsRequirementMet,
            'all_sessions_closed' => $allSessionsClosed,
            'can_complete' => $allMaterialsCompleted && $allSessionsRequirementMet && $allSessionsClosed,
            'reasons' => $reasons,
        ];
    }

    public function syncTopicCompletion(string $userId, string $topicId): TopicProgress
    {
        $snapshot = $this->topicCompletionSnapshot($userId, $topicId);

        $topic = Topic::query()
            ->findOrFail($topicId);

        $enrollment = $this->requireEnrollment($userId, $topic->course_id);

        return DB::transaction(function () use (
            $snapshot,
            $enrollment,
            $topicId,
            $topic,
            $userId
        ) {
            $topicProgress = TopicProgress::query()->firstOrNew([
                'course_enrollment_id' => $enrollment->id,
                'topic_id' => $topicId,
            ]);

            if ($snapshot['can_complete']) {
                $topicProgress->status = 'completed';
                $topicProgress->started_at ??= now();
                $topicProgress->completed_at = now();
            } else {
                $topicProgress->status = 'in_progress';
                $topicProgress->started_at ??= now();
                $topicProgress->completed_at = null;
            }

            $topicProgress->save();

            $this->syncCertificateState($userId, $topic->course_id);

            return $topicProgress;
        });
    }

    public function recalculateTopicCompletion(string $userId, string $topicId): TopicProgress
    {
        return $this->syncTopicCompletion($userId, $topicId);
    }

    public function syncCertificateState(string $userId, string $courseId): ?Certificate
    {
        $assessment = Assessment::query()
            ->where('course_id', $courseId)
            ->first();

        if (!$assessment) {
            return null;
        }

        $enrollment = CourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return null;
        }

        $topicIds = Topic::query()
            ->where('course_id', $courseId)
            ->pluck('id')
            ->all();

        $allTopicsCompleted = empty($topicIds)
            ? true
            : TopicProgress::query()
                ->where('course_enrollment_id', $enrollment->id)
                ->whereIn('topic_id', $topicIds)
                ->where('status', 'completed')
                ->count() === count($topicIds);

        $assessmentPassed = AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $userId)
            ->where('passed', true)
            ->whereNotNull('submitted_at')
            ->exists();

        $eligible = $allTopicsCompleted && $assessmentPassed;

        $certificate = Certificate::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($eligible) {
            $course = Course::query()->findOrFail($courseId);
            $user = User::query()->findOrFail($userId);

            return app(CertificateService::class)->issueCourseCertificate($course, $user);
        }

        if ($certificate && $certificate->status === 'issued') {
            $certificate->status = 'revoked';
            $certificate->save();
        }

        return $certificate;
    }

    private function syncEndedSessionAbsences(string $userId, Topic $topic): void
    {
        $endedSessions = $topic->videoSessions->filter(function ($session) {
            return $session->status !== 'cancelled'
                && $session->end_at
                && $session->end_at->lte(now());
        });

        foreach ($endedSessions as $session) {
            $attendance = Attendance::query()
                ->where('user_id', $userId)
                ->where('video_session_id', $session->id)
                ->first();

            if ($attendance) {
                continue;
            }

            Attendance::query()->create([
                'user_id' => $userId,
                'video_session_id' => $session->id,
                'status' => 'online',
                'check_in_at' => null,
                'clock_out_at' => null,
                'ip_address' => request()->ip(),
            ]);
        }
    }

    private function sessionHasEnded($session): bool
    {
        if (!$session->end_at) {
            return false;
        }

        return $session->end_at->lte(now());
    }
    private function requireEnrollment(string $userId, string $courseId): CourseEnrollment
    {
        $enrollment = CourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            throw ValidationException::withMessages([
                'course' => 'User belum terdaftar pada course ini.',
            ]);
        }

        return $enrollment;
    }

    public function getUserSummary(?User $user): array
    {
        if (!$user) {
            return [
                'courses_enrolled' => 0,
                'topics_completed' => 0,
                'certificates' => 0,
            ];
        }

        return [
            'courses_enrolled' => CourseEnrollment::where('user_id', $user->id)->count(),
            'topics_completed' => TopicProgress::whereHas('courseEnrollment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')->count(),
            'certificates' => Certificate::where('user_id', $user->id)->count(),
        ];
    }
}
