<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use App\Models\Certificate;
use App\Notifications\CertificateReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCertificateReadyEmail implements ShouldQueue
{
    public function handle(CertificateIssued $event): void
    {
        $certificate = Certificate::with([
            'user',
            'course'
        ])->findOrFail($event->certificateId);

        $certificate->user->notify(
            new CertificateReadyNotification($certificate)
        );
    }
}