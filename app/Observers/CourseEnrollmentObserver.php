<?php

namespace App\Observers;

use App\Models\CourseEnrollment;
use App\Models\Attendance;
use App\Models\VideoSession;
use App\Events\EnrollmentConfirmed;
use App\Events\CourseCompleted;
use Illuminate\Support\Facades\DB;

// Course Enrollment ['active', 'completed', 'dropped']

class CourseEnrollmentObserver
{
    /**
     * Handle the CourseEnrollment "created" event.
     */
    public function created(CourseEnrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment) {

            /**
             * Handle late enrollment attendance
             */
            $sessions = VideoSession::query()
                ->whereHas('topic', function ($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->where('end_at', '<', $enrollment->enrolled_at ?? now())
                ->get();

            foreach ($sessions as $session) {
                Attendance::firstOrCreate(
                    [
                        'video_session_id' => $session->id,
                        'user_id' => $enrollment->user_id,
                    ],
                    [
                        'status' => 'absent',
                        'check_in_at' => null,
                        'clock_out_at' => null,
                        'ip_address' => null,
                    ]
                );
            }

            /**
             * Dispatch event AFTER transaction committed
             */
            DB::afterCommit(function () use ($enrollment) {
                event(new EnrollmentConfirmed($enrollment->id));
            });
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