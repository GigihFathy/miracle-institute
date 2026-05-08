<?php

namespace App\Events;

class EnrollmentConfirmed
{
    public function __construct(
        public string $enrollmentId
    ) {}
}