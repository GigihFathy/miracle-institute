<?php

namespace App\Email\Listeners;

use App\Email\Events\VideoSessionScheduled;
use App\Jobs\ScheduleVideoSessionReminderJob;
use App\Models\VideoSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ScheduleVideoSessionReminder implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(VideoSessionScheduled $event): void
    {
        $session = VideoSession::query()->find($event->videoSessionId);

        if (! $session || ! $session->start_at || $session->start_at->isPast()) {
            return;
        }

        $reminderAt = $session->start_at->copy()->subMinutes(30);

        if ($reminderAt->isPast()) {
            return;
        }

        ScheduleVideoSessionReminderJob::dispatch($session->id)
            ->delay($reminderAt);
    }
}
