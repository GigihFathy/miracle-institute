<?php

namespace App\Observers;

use App\Email\Events\ContentCompleted;
use App\Events\AssessmentAvailable;
use App\Models\TopicProgress;
use App\Services\LearningProgressService;

class TopicProgressObserver
{
    public $afterCommit = true;

    public function updated(TopicProgress $topicProgress): void
    {
        if (
            $topicProgress->wasChanged('status') &&
            $topicProgress->status === 'completed'
        ) {
            event(new ContentCompleted('topic_progress', $topicProgress->id));

            $assessment = app(LearningProgressService::class)
                ->getAvailableAssessmentFor($topicProgress);

            if ($assessment) {
                event(new AssessmentAvailable(
                    $assessment->id,
                    $topicProgress->courseEnrollment->user_id
                ));
            }
        }
    }
}