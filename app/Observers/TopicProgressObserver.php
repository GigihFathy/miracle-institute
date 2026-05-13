<?php

namespace App\Observers;

use App\Email\Events\ContentCompleted;
use App\Models\TopicProgress;
use App\Services\LearningProgressService;
use Illuminate\Support\Facades\DB;

// Topic Progress ['not_started', 'in_progress', 'completed']

class TopicProgressObserver
{
    public function updated(TopicProgress $topicProgress): void
    {
        if (
            $topicProgress->wasChanged('status') &&
            $topicProgress->status === 'completed'
        ) {
            DB::afterCommit(function () use ($topicProgress) {
                event(new ContentCompleted('topic_progress', $topicProgress->id));
            });

            if ($assessment = app(LearningProgressService::class)
                ->getAvailableAssessmentFor($topicProgress)) {
                DB::afterCommit(function () use ($assessment, $topicProgress) {
                    event(new \App\Events\AssessmentAvailable(
                        $assessment->id,
                        $topicProgress->courseEnrollment->user_id
                    ));
                });
            }
        }
    }
}
