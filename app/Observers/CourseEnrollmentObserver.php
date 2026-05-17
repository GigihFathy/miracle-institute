<?php

namespace App\Observers;

use App\Email\Events\ContentCompleted;
use App\Email\Events\CourseEnrollmentCreated;
use App\Models\CourseEnrollment;
use App\Models\Attendance;
use App\Models\VideoSession;


class CourseEnrollmentObserver
{
    public $afterCommit = true;

    public function created(CourseEnrollment $enrollment): void
    {

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

        event(new CourseEnrollmentCreated($enrollment->id));
    }

    public function updated(CourseEnrollment $enrollment): void
    {
        if (
            $enrollment->wasChanged('status') &&
            $enrollment->status === 'completed'
        ) {
            event(new ContentCompleted('course_enrollment', $enrollment->id));
        }
    }
}