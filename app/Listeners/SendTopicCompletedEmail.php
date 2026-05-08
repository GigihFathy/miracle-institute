<?php

namespace App\Listeners;

use App\Events\TopicCompleted;
use App\Models\TopicProgress;
use App\Notifications\TopicCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTopicCompletedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(TopicCompleted $event): void
    {
        $progress = TopicProgress::query()
            ->with([
                'topic',
                'courseEnrollment',
                'courseEnrollment.user',
                'courseEnrollment.course',
            ])
            ->findOrFail($event->topicProgressId);

        $progress->courseEnrollment->user->notify(
            new TopicCompletedNotification($progress)
        );
    }
}