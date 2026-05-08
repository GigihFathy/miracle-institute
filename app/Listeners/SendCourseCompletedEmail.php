<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Models\CourseEnrollment;
use App\Notifications\CourseCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCourseCompletedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(CourseCompleted $event): void
    {
        $enrollment = CourseEnrollment::query()
            ->with([
                'user',
                'course',
            ])
            ->findOrFail($event->enrollmentId);

        $enrollment->user->notify(
            new CourseCompletedNotification($enrollment)
        );
    }
}