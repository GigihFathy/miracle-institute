<?php

namespace App\Email\Listeners;

use App\Email\Events\AttendanceIssueDetected;
use App\Models\Attendance;
use App\Notifications\AttendanceIssueNotification;

class SendAttendanceIssueNotification
{
   
    public function handle(AttendanceIssueDetected $event): void
    {
        $attendance = Attendance::with(['user', 'videoSession.topic.course'])
            ->findOrFail($event->attendanceId);

        $attendance->user->notify(
            new AttendanceIssueNotification($attendance, $event->issueType)
        );
    }
}