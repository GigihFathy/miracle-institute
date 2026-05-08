<?php

namespace App\Listeners;

use App\Events\EnrollmentConfirmed;
use App\Models\CourseEnrollment;
use App\Notifications\EnrollmentConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEnrollmentConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(EnrollmentConfirmed $event): void
    {
        $enrollment = CourseEnrollment::query()
            ->with([
                'user',
                'course',
            ])
            ->findOrFail($event->enrollmentId);

        $enrollment->user->notify(
            new EnrollmentConfirmedNotification($enrollment)
        );
    }
}