<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Events\CertificateIssued;

class CertificateObserver
{
    public $afterCommit = true;

    public function created(Certificate $certificate): void
    {
        event(new CertificateIssued($certificate->id));
    }
}