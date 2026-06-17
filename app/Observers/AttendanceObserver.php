<?php

namespace App\Observers;

use App\Email\Events\AttendanceIssueDetected;
use App\Models\Attendance;

class AttendanceObserver
{
    public $afterCommit = true;

    public function created(Attendance $attendance): void
    {
        $this->dispatchIssueIfNeeded($attendance);
    }

    public function updated(Attendance $attendance): void
    {
        if (!$attendance->wasChanged('status')) {
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

        if (in_array($attendance->status, ['online', 'absent'], true)) {
            $issueType = 'absent';
        }

        if (!$issueType) {
            return;
        }

        event(new AttendanceIssueDetected($attendance->id, $issueType));
    }
}
