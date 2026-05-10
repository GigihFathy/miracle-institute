<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\TopicProgress;
use App\Events\AssessmentAvailable;

class LearningProgressService
{
    public function checkAssessmentAvailability(
        TopicProgress $topicProgress
    ): void {

        $enrollment = $topicProgress
            ->courseEnrollment()
            ->with([
                'topicProgresses',
                'course.assessment',
            ])
            ->first();

        $unfinished = $enrollment
            ->topicProgresses
            ->where('status', '!=', 'completed')
            ->count();

        if ($unfinished > 0) {
            return;
        }

        $assessment = Assessment::query()
            ->where('course_id', $enrollment->course_id)
            ->where('status', 'active')
            ->first();

        if (!$assessment) {
            return;
        }

        event(new AssessmentAvailable(
            $assessment->id,
            $enrollment->user_id
        ));
    }
}