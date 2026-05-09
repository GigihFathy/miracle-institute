<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VideoSession;
use App\Notifications\VideoSessionReminderNotification;

class SendVideoSessionReminders extends Command
{
    protected $signature = 'sessions:reminders';

    protected $description = 'Send session reminders';

    public function handle(): int
    {
        $sessions = VideoSession::query()
            ->whereBetween('start_at', [
                now(),
                now()->addMinutes(30)
            ])
            ->whereNull('reminder_sent_at')
            ->get();

        foreach ($sessions as $session) {

            $enrollments = $session
                ->topic
                ->course
                ->enrollments;

            foreach ($enrollments as $enrollment) {

                $enrollment->user->notify(
                    new VideoSessionReminderNotification($session)
                );
            }

            $session->update([
                'reminder_sent_at' => now()
            ]);
        }

        return self::SUCCESS;
    }
}