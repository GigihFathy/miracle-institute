<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\TopicProgress;

class LearningProgressService
{
    public function getAvailableAssessmentFor(
        TopicProgress $topicProgress
    ): ?Assessment {

        $enrollment = $topicProgress
            ->courseEnrollment()
            ->with([
                'topicProgresses.topic',
                'course.assessment',
            ])
            ->first();

        $unfinished = $enrollment
            ->topicProgresses
            ->filter(fn ($tp) => $tp->topic?->status !== 'archived')
            ->where('status', '!=', 'completed')
            ->count();

        if ($unfinished > 0) {
            return null;
        }

        return Assessment::query()
            ->where('course_id', $enrollment->course_id)
            ->where('status', 'active')
            ->first();
    }
}