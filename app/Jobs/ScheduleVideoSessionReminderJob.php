<?php

namespace App\Jobs;

use App\Events\VideoSessionReminderTriggered;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\VideoSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleVideoSessionReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $videoSessionId,
        public string $reminderType = 'h2',
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $session = VideoSession::query()->find($this->videoSessionId);

        if (! $session || ! $session->start_at || $session->start_at->isPast()) {
            return;
        }

        event(new VideoSessionReminderTriggered($session->id, $this->reminderType));
    }
}
