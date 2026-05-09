<?php

namespace App\Listeners;

use App\Events\TopicCompleted;
use App\Models\TopicProgress;
use App\Notifications\TopicCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTopicCompletedEmail implements ShouldQueue
{
    public function handle(TopicCompleted $event): void
    {
        $progress = TopicProgress::with([
            'courseEnrollment.user',
            'topic'
        ])->findOrFail($event->topicProgressId);

        $progress->courseEnrollment->user->notify(
            new TopicCompletedNotification($progress)
        );
    }
}