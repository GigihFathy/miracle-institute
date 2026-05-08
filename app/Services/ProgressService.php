<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;

class ProgressService
{
    public function markMaterialViewed(string $userId, string $materialId): MaterialProgress
    {
        $material = Material::with('topic')->findOrFail($materialId);

        $progress = MaterialProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'material_id' => $materialId,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        $this->checkTopicCompletion($userId, $material->topic_id, $material->topic->course_id);

        return $progress;
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