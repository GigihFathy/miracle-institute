<?php

namespace App\Email\Events;

class CourseEnrollmentCreated
{
    public function __construct(
        public string $enrollmentId
    ) {}
}
