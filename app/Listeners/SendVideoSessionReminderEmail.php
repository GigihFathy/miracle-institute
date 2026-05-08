<?php

namespace App\Listeners;

use App\Events\VideoSessionReminderTriggered;
use App\Models\VideoSession;
use App\Notifications\VideoSessionReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVideoSessionReminderEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(VideoSessionReminderTriggered $event): void
    {
        $session = VideoSession::query()
            ->with([
                'topic',
                'topic.course',
                'topic.course.enrollments',
                'topic.course.enrollments.user',
            ])
            ->findOrFail($event->videoSessionId);

        foreach ($session->topic->course->enrollments as $enrollment) {

            if (! $enrollment->user) {
                continue;
            }

            $enrollment->user->notify(
                new VideoSessionReminderNotification($session)
            );
        }
    }
}