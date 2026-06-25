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

        $h2At = $session->start_at->copy()->subDays(2);

        if (! $h2At->isPast()) {
            ScheduleVideoSessionReminderJob::dispatch($session->id, 'h2')
                ->delay($h2At)
                ->onQueue('emails');
        }

        $h1At = $session->start_at->copy()->subHour();

        if (! $h1At->isPast()) {
            ScheduleVideoSessionReminderJob::dispatch($session->id, 'h1')
                ->delay($h1At)
                ->onQueue('emails');
        }
    }
}
