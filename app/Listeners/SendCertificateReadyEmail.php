<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use App\Models\Certificate;
use App\Notifications\CertificateReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCertificateReadyEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(CertificateIssued $event): void
    {
        $certificate = Certificate::query()
            ->with([
                'user',
                'course',
            ])
            ->findOrFail($event->certificateId);

        $certificate->user->notify(
            new CertificateReadyNotification($certificate)
        );
    }
}