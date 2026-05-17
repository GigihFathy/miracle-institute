<?php

namespace App\Observers;

use App\Email\Events\CertificateIssued;
use App\Models\Certificate;

class CertificateObserver
{
    public $afterCommit = true;

    public function created(Certificate $certificate): void
    {
        event(new CertificateIssued($certificate->id));
    }
}