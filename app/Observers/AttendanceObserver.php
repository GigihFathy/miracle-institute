<?php

namespace App\Observers;

use App\Email\Events\AttendanceIssueDetected;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

// Attendance Observer ['present', 'late', 'absent']

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $this->dispatchIssueIfNeeded($attendance);
    }

    public function updated(Attendance $attendance): void
    {
        if (! $attendance->wasChanged('status')) {
            return;
        }

        $this->dispatchIssueIfNeeded($attendance);
    }

    private function dispatchIssueIfNeeded(Attendance $attendance): void
    {
        $attendance->loadMissing('videoSession');

        $issueType = null;

        if (
            $attendance->status === 'late' &&
            $attendance->check_in_at &&
            $attendance->videoSession &&
            $attendance->check_in_at->greaterThan($attendance->videoSession->start_at)
        ) {
            $issueType = 'late_join';
        }

        if ($attendance->status === 'absent') {
            $issueType = 'absent';
        }

        if (! $issueType) {
            return;
        }

        DB::afterCommit(function () use ($attendance, $issueType) {
            event(new AttendanceIssueDetected($attendance->id, $issueType));
        });
    }
}