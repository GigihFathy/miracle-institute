<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Services\LearningNotificationService;

class CertificateObserver
{
    public function __construct(protected LearningNotificationService $notifier) {}

    public function created(Certificate $certificate): void
    {
        $certificate->loadMissing(['user', 'course']);
        $this->notifier->sendCertificateIssued($certificate);
    }
}