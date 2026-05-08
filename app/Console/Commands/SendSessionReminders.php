<?php

namespace App\Console\Commands;

use App\Models\VideoSession;
use App\Services\LearningNotificationService;
use Illuminate\Console\Command;

class SendSessionReminders extends Command
{
    protected $signature = 'learning:send-session-reminders';
    protected $description = 'Send automatic session reminders';

    public function __construct(protected LearningNotificationService $notifier)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $sessions = VideoSession::with(['topic.course.enrollments.user'])
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereBetween('start_at', [now()->addHour(), now()->addHours(2)])
            ->get();

        foreach ($sessions as $session) {
            $enrollments = $session->topic->course->enrollments;

            foreach ($enrollments as $enrollment) {
                $this->notifier->sendSessionReminder(
                    $session,
                    (int) $enrollment->user_id,
                    $session->start_at->format('YmdH')
                );
            }
        }

        return self::SUCCESS;
    }
}