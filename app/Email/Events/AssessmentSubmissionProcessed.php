<?php

namespace App\Email\Events;

class AssessmentSubmissionProcessed
{
    public function __construct(
        public string $attemptId,
        public bool $passed
    ) {}
}
