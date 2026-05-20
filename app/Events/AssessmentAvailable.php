<?php

namespace App\Events;

class AssessmentAvailable
{
    public function __construct(
        public string $assessmentId,
        public string $userId
    ) {}
}
