<?php

namespace App\Events;

class CertificateIssued
{
    public function __construct(
        public string $certificateId
    ) {}
}