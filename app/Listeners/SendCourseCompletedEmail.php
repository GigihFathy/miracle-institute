<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Models\CourseEnrollment;
use App\Notifications\CourseCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseCompletedEmail implements ShouldQueue
{
    public function handle(CourseCompleted $event): void
    {
        $enrollment = CourseEnrollment::with([
            'user',
            'course'
        ])->findOrFail($event->enrollmentId);

        $enrollment->user->notify(
            new CourseCompletedNotification($enrollment)
        );
    }
}