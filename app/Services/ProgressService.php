<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ProgressService
{
    public function markMaterialViewed(string $userId, string $materialId): void
    {
        $material = Material::with('topic.videoSessions')->findOrFail($materialId);

        DB::transaction(function () use ($userId, $material) {
            MaterialProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'material_id' => $material->id,
                ],
                [
                    'status' => 'completed',
                    'started_at' => now(),
                    'completed_at' => now(),
                ]
            );

            $this->recalculateTopicCompletion($userId, $material->topic_id);
        });
    }

    public function recalculateTopicCompletion(string $userId, string $topicId): void
    {
        $topic = \App\Models\Topic::with(['materials', 'videoSessions'])->findOrFail($topicId);

        $activeMaterials = $topic->materials()->where('status', 'active')->pluck('id');
        $sessionIds = $topic->videoSessions()->whereIn('status', ['scheduled', 'ongoing', 'completed'])->pluck('id');

        $materialsDone = $activeMaterials->isEmpty()
            || MaterialProgress::query()
                ->where('user_id', $userId)
                ->whereIn('material_id', $activeMaterials)
                ->where('status', 'completed')
                ->count() >= $activeMaterials->count();

        $sessionsDone = $sessionIds->isEmpty()
            || \App\Models\Attendance::query()
                ->where('user_id', $userId)
                ->whereIn('video_session_id', $sessionIds)
                ->whereIn('status', ['present', 'late'])
                ->count() >= $sessionIds->count();

        $enrollment = \App\Models\CourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $topic->course_id)
            ->first();

        if (! $enrollment) {
            return;
        }

        $progress = TopicProgress::firstOrNew([
            'course_enrollment_id' => $enrollment->id,
            'topic_id' => $topicId,
        ]);

        if ($materialsDone && $sessionsDone) {
            $progress->status = 'completed';
            $progress->completed_at = now();
            $progress->started_at ??= now();
        } else {
            $progress->status = $progress->exists ? ($progress->status ?: 'started') : 'started';
            $progress->started_at ??= now();
        }

        $progress->save();
    }

    protected function checkTopicCompletion(string $userId, string $topicId, string $courseId): void
    {
        $topic = Topic::with('materials')->findOrFail($topicId);

        $totalMaterials = $topic->materials->count();
        if ($totalMaterials === 0) {
            return;
        }

        $completedMaterials = MaterialProgress::query()
            ->where('user_id', $userId)
            ->whereIn('material_id', $topic->materials->pluck('id'))
            ->where('status', 'completed')
            ->count();

        if ($completedMaterials >= $totalMaterials) {
            $enrollment = CourseEnrollment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            $this->markTopicCompleted($userId, $topicId, $courseId, $enrollment->id);
        }
    }

    public function markTopicCompleted(string $userId, string $topicId, string $courseId, string $enrollmentId): TopicProgress
    {
        $existingStatus = TopicProgress::where('course_enrollment_id', $enrollmentId)
            ->where('topic_id', $topicId)
            ->value('status');

        $progress = TopicProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollmentId,
                'topic_id' => $topicId,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        return $progress;
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