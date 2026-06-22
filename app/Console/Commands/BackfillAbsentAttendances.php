<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\CourseEnrollment;
use App\Models\VideoSession;
use Illuminate\Console\Command;

class BackfillAbsentAttendances extends Command
{
    protected $signature = 'attendance:backfill-absent';
    protected $description = 'Backfill online attendance for ended sessions';

    public function handle(): int
    {
        VideoSession::query()
            ->with(['topic.course'])
            ->whereNotNull('end_at')
            ->where('end_at', '<=', now())
            ->chunkById(100, function ($sessions) {
                foreach ($sessions as $session) {
                    $enrollments = CourseEnrollment::query()
                        ->where('course_id', $session->topic?->course_id)
                        ->get(['user_id']);

                    foreach ($enrollments as $enrollment) {
                        Attendance::firstOrCreate(
                            [
                                'video_session_id' => $session->id,
                                'user_id' => $enrollment->user_id,
                            ],
                            [
                                'status' => 'online',
                                'check_in_at' => null,
                                'clock_out_at' => null,
                                'ip_address' => null,
                            ]
                        );
                    }
                }
            });

        return self::SUCCESS;
    }
}
