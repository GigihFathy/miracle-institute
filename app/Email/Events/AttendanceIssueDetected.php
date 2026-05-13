<?php

namespace App\Email\Events;

class AttendanceIssueDetected
{
    public function __construct(
        public string $attendanceId,
        public string $issueType
    ) {}
}
