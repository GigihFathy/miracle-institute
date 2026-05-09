<?php

namespace App\Observers;

use App\Models\CourseEnrollment;
use App\Events\EnrollmentConfirmed;
use App\Events\CourseCompleted;
use Illuminate\Support\Facades\DB;

// Course Enrollment ['active', 'completed', 'dropped']

class CourseEnrollmentObserver
{
    public function created(CourseEnrollment $enrollment): void
    {
        DB::afterCommit(function () use ($enrollment) {
            app(AttendanceAutomationService::class)
                ->backfillAbsentForLateEnrollment($enrollment);

            event(new EnrollmentConfirmed($enrollment->id));
        });
    }

    public function updated(CourseEnrollment $enrollment): void
    {
        if (
            $enrollment->wasChanged('status') &&
            $enrollment->status === 'completed'
        ) {
            DB::afterCommit(function () use ($enrollment) {
                event(new CourseCompleted($enrollment->id));
            });
        }
    }
}
