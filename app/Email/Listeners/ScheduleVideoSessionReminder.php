<?php

namespace App\Email\Listeners;

use App\Email\Events\VideoSessionScheduled;
use App\Jobs\ScheduleVideoSessionReminderJob;
use App\Models\VideoSession;

class ScheduleVideoSessionReminder
{
    public function handle(VideoSessionScheduled $event): void
    {
        $session = VideoSession::query()->find($event->videoSessionId);

        if (! $session || ! $session->start_at || $session->start_at->isPast()) {
            return;
        }

        $reminderAt = $session->start_at->copy()->subDays(2);

        if ($reminderAt->isPast()) {
            return;
        }

        ScheduleVideoSessionReminderJob::dispatch($session->id)
            ->delay($reminderAt)
            ->onQueue('emails');
    }
}
