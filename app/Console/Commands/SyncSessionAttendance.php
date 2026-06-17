<?php
namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\CourseEnrollment;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\VideoSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;



// Menyesuaikan Session yang sedang on going dan completed dan Attendances berkaitan session tersebut
class SyncSessionAttendance extends Command
{
    protected $signature = 'sessions:sync-attendance';
    protected $description = 'Auto mark online attendances and complete topics when sessions end';

    public function handle(): int
    {
        $now = now();

        $sessions = VideoSession::query()
            ->with('topic.course')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->where('end_at', '<=', $now)
            ->get();

        foreach ($sessions as $session) {
            DB::transaction(function () use ($session, $now) {
                $topic = $session->topic;

                $enrollments = CourseEnrollment::query()
                    ->where('course_id', $topic->course_id)
                    ->get(['id', 'user_id']);

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

                $session->update([
                    'status' => 'completed',
                ]);

                $topicHasRemainingSessions = $topic->videoSessions()
                    ->where('end_at', '>', $now)
                    ->exists();

                if (!$topicHasRemainingSessions) {
                    TopicProgress::query()
                        ->where('topic_id', $topic->id)
                        ->whereIn('course_enrollment_id', $enrollments->pluck('id'))
                        ->update([
                            'status' => 'completed',
                            'completed_at' => $now,
                        ]);
                }
            });
        }

        return self::SUCCESS;
    }
}
