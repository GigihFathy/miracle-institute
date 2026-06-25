<?php

namespace App\Email\Listeners;

use App\Email\Events\CertificateIssued;
use App\Models\Certificate;
use App\Notifications\CertificateReadyNotification;

class SendCertificateReadyEmail 
{
    public function handle(CertificateIssued $event): void
    {
        return;

        $certificate = Certificate::with([
            'user',
            'course'
        ])->findOrFail($event->certificateId);

        
        $certificate->user->notify(
            new CertificateReadyNotification($certificate)
        );
    }
}