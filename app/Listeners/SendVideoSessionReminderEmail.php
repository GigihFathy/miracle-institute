<?php

namespace App\Listeners;

use App\Events\VideoSessionReminderTriggered;
use App\Models\VideoSession;
use App\Notifications\VideoSessionReminderNotification;

class SendVideoSessionReminderEmail
{

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

            if (!$enrollment->user) {
                continue;
            }

            $enrollment->user->notify(
                new VideoSessionReminderNotification($session)
            );
        }
    }
}