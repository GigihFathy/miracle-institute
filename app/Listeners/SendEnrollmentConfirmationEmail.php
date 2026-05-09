<?php

namespace App\Listeners;

use App\Events\EnrollmentConfirmed;
use App\Models\CourseEnrollment;
use App\Notifications\EnrollmentConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEnrollmentConfirmationEmail implements ShouldQueue
{
    public function handle(EnrollmentConfirmed $event): void
    {
        dd($event);

        $enrollment = CourseEnrollment::with([
            'user',
            'course'
        ])->findOrFail($event->enrollmentId);

        $enrollment->user->notify(
            new EnrollmentConfirmedNotification($enrollment)
        );
    }
}