<?php

namespace App\Listeners;

use App\Events\EnrollmentConfirmed;
use App\Mail\EnrollmentConfirmedMail;
use Illuminate\Support\Facades\Mail;

class SendEnrollmentConfirmedMail
{
    public function handle(EnrollmentConfirmed $event): void
    {
        Mail::to($event->enrollment->user->email)->queue(new EnrollmentConfirmedMail($event->enrollment));
    }
}