<?php

namespace App\Events;

class AssessmentSubmitted
{
    public function __construct(
        public string $attemptId
    ) {}
}