<?php

namespace App\Observers;

use App\Models\TopicProgress;
use App\Events\TopicCompleted;
use App\Services\LearningProgressService;
use Illuminate\Support\Facades\DB;

class TopicProgressObserver
{
    public function updated(TopicProgress $topicProgress): void
    {
        if (
            $topicProgress->wasChanged('status') &&
            $topicProgress->status === 'completed'
        ) {
            DB::afterCommit(function () use ($topicProgress) {
                event(new TopicCompleted($topicProgress->id));
            });

            app(LearningProgressService::class)
                ->checkAssessmentAvailability($topicProgress);
        }
    }
}