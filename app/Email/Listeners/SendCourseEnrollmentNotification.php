<?php

namespace App\Email\Listeners;

use App\Email\Events\CourseEnrollmentCreated;
use App\Models\CourseEnrollment;
use App\Notifications\EnrollmentConfirmedNotification;

class SendCourseEnrollmentNotification
{
    public function handle(CourseEnrollmentCreated $event): void
    {
        $enrollment = CourseEnrollment::with(['user', 'course'])
            ->findOrFail($event->enrollmentId);

        $enrollment->user->notify(
            new EnrollmentConfirmedNotification($enrollment)
        );
    }
}
