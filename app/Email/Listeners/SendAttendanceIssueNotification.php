<?php

namespace App\Email\Listeners;

use App\Email\Events\AttendanceIssueDetected;
use App\Models\Attendance;
use App\Email\Notifications\AttendanceIssueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAttendanceIssueNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(AttendanceIssueDetected $event): void
    {
        $attendance = Attendance::with(['user', 'videoSession.topic.course'])
            ->findOrFail($event->attendanceId);

        $attendance->user->notify(
            new AttendanceIssueNotification($attendance, $event->issueType)
        );
    }
}
