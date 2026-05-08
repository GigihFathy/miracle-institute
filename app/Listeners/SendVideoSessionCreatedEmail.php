<?php

namespace App\Listeners;

use App\Events\VideoSessionCreated;
use App\Models\CourseEnrollment;
use App\Models\VideoSession;
use App\Notifications\VideoSessionCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendVideoSessionCreatedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public int $tries = 3;

    public int $timeout = 120;

    public function handle(VideoSessionCreated $event): void
    {
        $session = VideoSession::query()
            ->with([
                'topic.course',
            ])
            ->findOrFail($event->videoSessionId);

        CourseEnrollment::query()
            ->with(['user'])
            ->where('course_id', $session->topic->course_id)
            ->where('status', 'enrolled')
            ->chunkById(200, function ($enrollments) use ($session) {
                $users = $enrollments
                    ->pluck('user')
                    ->filter()
                    ->values();

                if ($users->isEmpty()) {
                    return;
                }

                Notification::send(
                    $users,
                    new VideoSessionCreatedNotification($session)
                );
            });
    }
}