<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Notifications\CourseEnrollmentNotification;

class SendEnrollmentNotification
{
    public function handle(EnrollmentCreated $event): void
    {
        $event->enrollment->user->notify(new CourseEnrollmentNotification($event->enrollment));
    }
}