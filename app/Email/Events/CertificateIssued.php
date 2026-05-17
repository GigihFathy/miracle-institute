<?php

namespace App\Email\Events;

class CertificateIssued
{
    public function __construct(
        public string $certificateId
    ) {}
}