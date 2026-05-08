<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CourseEnrollment;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;
use App\Models\VideoSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceAutomationService
{
    public function recordSessionAccess(VideoSession $session, User $user, ?string $ipAddress = null): Attendance
    {
        if (! $session->start_at || ! $session->end_at) {
            throw new \RuntimeException('Session schedule is incomplete.');
        }

        $now = now();

        if ($now->gt($session->end_at)) {
            throw new \RuntimeException('Session already ended.');
        }

        $attendance = DB::transaction(function () use ($session, $user, $ipAddress, $now) {
            $existing = Attendance::query()
                ->where('video_session_id', $session->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // Jika sudah ada record, jangan timpa data yang sudah valid.
                if (blank($existing->check_in_at)) {
                    $existing->forceFill([
                        'check_in_at' => $now,
                        'status' => $this->resolveStatus($session, $now),
                        'ip_address' => $ipAddress,
                    ])->save();
                }

                return $existing->refresh();
            }

            $attendance = new Attendance();
            $attendance->forceFill([
                'video_session_id' => $session->id,
                'user_id' => $user->id,
                'status' => $this->resolveStatus($session, $now),
                'check_in_at' => $now,
                'ip_address' => $ipAddress,
            ]);
            $attendance->save();

            return $attendance;
        });

        $this->recalculateTopicProgress($session, $user);

        return $attendance;
    }

    public function backfillAbsentForEndedSession(VideoSession $session): int
    {
        if (! $session->start_at || ! $session->end_at) {
            return 0;
        }

        if (now()->lt($session->end_at)) {
            return 0;
        }

        $course = $session->topic?->course;
        if (! $course) {
            return 0;
        }

        $enrollments = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->get();

        $created = 0;

        foreach ($enrollments as $enrollment) {
            $attendanceExists = Attendance::query()
                ->where('video_session_id', $session->id)
                ->where('user_id', $enrollment->user_id)
                ->exists();

            if ($attendanceExists) {
                continue;
            }

            Attendance::create([
                'video_session_id' => $session->id,
                'user_id' => $enrollment->user_id,
                'status' => 'absent',
                'check_in_at' => null,
                'clock_out_at' => null,
                'ip_address' => null,
            ]);

            $created++;
            $this->recalculateTopicProgress($session, $enrollment->user);
        }

        return $created;
    }

    public function backfillAbsentForLateEnrollment(CourseEnrollment $enrollment): int
    {
        $course = $enrollment->course()->with('topics.videoSessions')->first();
        if (! $course) {
            return 0;
        }

        $created = 0;

        foreach ($course->topics as $topic) {
            foreach ($topic->videoSessions as $session) {
                if (! $session->end_at || now()->lt($session->end_at)) {
                    continue;
                }

                $attendanceExists = Attendance::query()
                    ->where('video_session_id', $session->id)
                    ->where('user_id', $enrollment->user_id)
                    ->exists();

                if ($attendanceExists) {
                    continue;
                }

                Attendance::create([
                    'video_session_id' => $session->id,
                    'user_id' => $enrollment->user_id,
                    'status' => 'absent',
                    'check_in_at' => null,
                    'clock_out_at' => null,
                    'ip_address' => null,
                ]);

                $created++;
            }

            $this->recalculateTopicProgress($topic->videoSessions->first(), $enrollment->user);
        }

        return $created;
    }

    public function recalculateTopicProgress(VideoSession $session, User $user): void
    {
        $topic = $session->topic()->with(['materials', 'videoSessions'])->first();
        if (! $topic) {
            return;
        }

        $enrollment = $user->courseEnrollments()
            ->where('course_id', $topic->course_id)
            ->first();

        if (! $enrollment) {
            return;
        }

        $allMaterialsDone = $this->allMaterialsDone($topic->id, $user->id);
        $allSessionsDone = $this->allSessionsDone($topic->id, $user->id);

        $progress = TopicProgress::firstOrNew([
            'course_enrollment_id' => $enrollment->id,
            'topic_id' => $topic->id,
        ]);

        if ($allMaterialsDone && $allSessionsDone) {
            $progress->status = 'completed';
            $progress->completed_at = now();
            $progress->started_at ??= now()->subMinutes(5);
        } elseif ($progress->exists) {
            if (blank($progress->started_at)) {
                $progress->started_at = now();
            }
            $progress->status = in_array($progress->status, ['completed', 'started'], true)
                ? $progress->status
                : 'started';
        } else {
            $progress->status = 'started';
            $progress->started_at = now();
        }

        $progress->save();
    }

    private function allMaterialsDone(string $topicId, string $userId): bool
    {
        $requiredMaterials = DB::table('materials')
            ->where('topic_id', $topicId)
            ->where('status', 'active')
            ->count();

        if ($requiredMaterials === 0) {
            return true;
        }

        $doneMaterials = DB::table('material_progresses')
            ->join('materials', 'materials.id', '=', 'material_progresses.material_id')
            ->where('materials.topic_id', $topicId)
            ->where('material_progresses.user_id', $userId)
            ->where('material_progresses.status', 'completed')
            ->count();

        return $doneMaterials >= $requiredMaterials;
    }

    private function allSessionsDone(string $topicId, string $userId): bool
    {
        $sessions = VideoSession::query()
            ->where('topic_id', $topicId)
            ->whereIn('status', ['scheduled', 'ongoing', 'completed'])
            ->count();

        if ($sessions === 0) {
            return true;
        }

        $done = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('video_sessions.topic_id', $topicId)
            ->where('attendances.user_id', $userId)
            ->whereIn('attendances.status', ['present', 'late'])
            ->count();

        return $done >= $sessions;
    }

    private function resolveStatus(VideoSession $session, Carbon $now): string
    {
        $deadline = $this->clockInDeadline($session);

        if ($now->lessThanOrEqualTo($deadline)) {
            return 'present';
        }

        return 'late';
    }

    private function clockInDeadline(VideoSession $session): Carbon
    {
        $startWindow = $session->start_at->copy()->addMinutes(45);
        $endWindow = $session->end_at->copy()->subMinutes(15);

        return $startWindow->lt($endWindow) ? $startWindow : $endWindow;
    }
}