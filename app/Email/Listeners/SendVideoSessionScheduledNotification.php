<?php

namespace App\Email\Listeners;

use App\Email\Events\VideoSessionScheduled;
use App\Models\VideoSession;
use App\Notifications\VideoSessionCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVideoSessionScheduledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(VideoSessionScheduled $event): void
    {
        $session = VideoSession::with(['topic.course.enrollments.user'])
            ->findOrFail($event->videoSessionId);

        foreach ($session->topic->course->enrollments as $enrollment) {
            if (! $enrollment->user) {
                continue;
            }

            $enrollment->user->notify(
                new VideoSessionCreatedNotification($session)
            );
        }
    }
}
