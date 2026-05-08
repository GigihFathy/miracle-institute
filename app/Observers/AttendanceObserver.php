<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Events\AttendanceIssueDetected;
use Illuminate\Support\Facades\DB;

class AttendanceObserver
{
    public function updated(Attendance $attendance): void
    {
        if (
            $attendance->wasChanged('status') &&
            in_array($attendance->status, ['issue', 'absent'])
        ) {
            DB::afterCommit(function () use ($attendance) {
                event(new AttendanceIssueDetected($attendance->id));
            });
        }
    }
}