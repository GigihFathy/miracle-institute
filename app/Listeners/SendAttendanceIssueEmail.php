<?php

namespace App\Listeners;

use App\Events\AttendanceIssueDetected;
use App\Models\Attendance;
use App\Notifications\AttendanceIssueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAttendanceIssueEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(AttendanceIssueDetected $event): void
    {
        $attendance = Attendance::query()
            ->with([
                'user',
                'videoSession',
                'videoSession.topic',
            ])
            ->findOrFail($event->attendanceId);

        $attendance->user->notify(
            new AttendanceIssueNotification($attendance)
        );
    }
}