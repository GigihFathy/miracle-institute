<?php

namespace App\Listeners;

use App\Events\AttendanceIssueDetected;
use App\Models\Attendance;
use App\Notifications\AttendanceIssueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAttendanceIssueEmail implements ShouldQueue
{
    public function handle(AttendanceIssueDetected $event): void
    {
        $attendance = Attendance::with('user')
            ->findOrFail($event->attendanceId);

        $attendance->user->notify(
            new AttendanceIssueNotification($attendance)
        );
    }
}