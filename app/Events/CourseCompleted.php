<?php

namespace App\Events;

class CourseCompleted
{
    public function __construct(
        public string $enrollmentId
    ) {}
}