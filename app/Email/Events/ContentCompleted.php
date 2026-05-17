<?php

namespace App\Email\Events;

class ContentCompleted
{
    public function __construct(
        public string $subjectType,
        public string $subjectId
    ) {}
}
