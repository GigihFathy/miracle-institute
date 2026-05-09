<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Events\CertificateIssued;
use Illuminate\Support\Facades\DB;

class CertificateObserver
{
    public function created(Certificate $certificate): void
    {
        DB::afterCommit(function () use ($certificate) {
            event(new CertificateIssued($certificate->id));
        });
    }
}