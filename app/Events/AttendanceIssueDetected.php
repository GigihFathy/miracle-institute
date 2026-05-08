<?php

namespace App\Events;

class AttendanceIssueDetected
{
    public function __construct(
        public string $attendanceId
    ) {}
}