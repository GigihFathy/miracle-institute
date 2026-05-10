<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueCourseCertificate(Course $course, User $user): Certificate
    {
        return DB::transaction(function () use ($course, $user) {
            $enrollment = CourseEnrollment::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = Certificate::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->where('type', 'course')
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === 'issued' && $existing->file_path) {
                return $existing;
            }

            $topics = $course->topics()
                ->with(['videoSessions' => fn ($q) => $q->orderBy('start_at')])
                ->orderBy('sort_order')
                ->get();

            if ($topics->isEmpty()) {
                throw new \RuntimeException('Course belum memiliki topik, sertifikat tidak dapat diterbitkan.');
            }

            $allCompleted = $topics->every(function ($topic) use ($enrollment) {
                return TopicProgress::query()
                    ->where('course_enrollment_id', $enrollment->id)
                    ->where('topic_id', $topic->id)
                    ->where('status', 'completed')
                    ->exists();
            });

            if (!$allCompleted) {
                throw new \RuntimeException('Course belum selesai, sertifikat belum bisa diterbitkan.');
            }

            $sequence = Certificate::query()
                ->where('course_id', $course->id)
                ->where('type', 'course')
                ->lockForUpdate()
                ->count() + 1;

            $certificateNumber = $this->generateCertificateNumber($course, $sequence);
            $issuedAt = now();

            $payload = [
                'certificateNumber' => $certificateNumber,
                'courseCode' => $this->courseCode($course),
                'course' => $course,
                'user' => $user,
                'issuedAt' => $issuedAt,
                'frontDate' => $this->formatDateLong($issuedAt),
                'sequenceLabel' => str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                'frontSummary' => $this->buildFrontSummary($course, $user, $topics->count()),
                'backTopics' => $this->buildBackTopics($course, $user),
                'achievementSummary' => $this->buildAchievementSummary($course, $user, $topics->count()),
                'logoBase64' => $this->placeholderImageBase64(),
            ];

            $pdf = Pdf::loadView('pdf.certificates.course', $payload)
                ->setPaper('a4', 'portrait');

            $certificate = $existing ?? new Certificate();

            $certificate->forceFill([
                'certificate_number' => $certificateNumber,
                'user_id' => $user->id,
                'type' => 'course',
                'course_id' => $course->id,
                'topic_id' => null,
                'issued_at' => $issuedAt,
                'status' => 'issued',
            ])->save();

            return $certificate->fresh();
        });
    }

    public function downloadCourseCertificate(
        Certificate $certificate,
        string $filename
    ) {
        $course = $certificate->course()->with([
            'topics.videoSessions'
        ])->firstOrFail();

        $user = $certificate->user()->firstOrFail();

        $issuedAt = $certificate->issued_at ?? now();

        $topics = $course->topics()
            ->orderBy('sort_order')
            ->get();

        $pdf = Pdf::loadView('pdf.certificates.course', [
            'certificateNumber' => $certificate->certificate_number,
            'courseCode' => $this->courseCode($course),
            'course' => $course,
            'user' => $user,
            'issuedAt' => $issuedAt,
            'frontDate' => $this->formatDateLong($issuedAt),
            'sequenceLabel' => $this->extractSequenceLabel($certificate->certificate_number),
            'frontSummary' => $this->buildFrontSummary($course, $user, $topics->count()),
            'backTopics' => $this->buildBackTopics($course, $user),
            'achievementSummary' => $this->buildAchievementSummary($course, $user, $topics->count()),
            'logoBase64' => $this->placeholderImageBase64(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function buildBackTopics(Course $course, User $user): array
    {
        $topics = $course->topics()
            ->with(['videoSessions' => fn ($q) => $q->orderBy('start_at')])
            ->orderBy('sort_order')
            ->get();

        return $topics->map(function ($topic) use ($user) {
            $sessions = [];

            foreach ($topic->videoSessions as $session) {
                $attendance = Attendance::query()
                    ->where('user_id', $user->id)
                    ->where('video_session_id', $session->id)
                    ->first();

                $status = match ($attendance?->status) {
                    'present' => 'Online',
                    'late' => 'Susulan',
                    default => 'Absent',
                };

                $sessions[] = [
                    'session_title' => $session->title,
                    'attendance_status' => $status,
                    'attendance_date' => $attendance?->check_in_at
                        ? $this->formatDateLong($attendance->check_in_at)
                        : '-',
                ];
            }

            return [
                'topic_name' => $topic->name,
                'topic_status' => $this->topicCompletionStatus($topic->id, $user->id),
                'sessions' => $sessions,
            ];
        })->all();
    }

    public function buildAchievementSummary(Course $course, User $user, int $totalTopics): array
    {
        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        $completedTopics = $enrollment
            ? TopicProgress::query()
                ->where('course_enrollment_id', $enrollment->id)
                ->where('status', 'completed')
                ->count()
            : 0;

        $present = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('attendances.user_id', $user->id)
            ->whereIn('attendances.status', ['present', 'late'])
            ->count();

        $late = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('attendances.user_id', $user->id)
            ->where('attendances.status', 'late')
            ->count();

        $absent = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('attendances.user_id', $user->id)
            ->where('attendances.status', 'absent')
            ->count();

        return [
            'topics_total' => $totalTopics,
            'topics_completed' => $completedTopics,
            'attendance_present' => $present,
            'attendance_late' => $late,
            'attendance_absent' => $absent,
        ];
    }

    private function topicCompletionStatus(string $topicId, string $userId): string
    {
        $present = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('video_sessions.topic_id', $topicId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.status', 'present')
            ->exists();

        if ($present) {
            return 'Present';
        }

        $late = Attendance::query()
            ->join('video_sessions', 'video_sessions.id', '=', 'attendances.video_session_id')
            ->where('video_sessions.topic_id', $topicId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.status', 'late')
            ->exists();

        return $late ? 'Susulan' : 'Absent';
    }

    private function generateCertificateNumber(Course $course, int $sequence): string
    {
        return sprintf(
            'CERT-%s-%s-%04d',
            $this->courseCode($course),
            now()->format('Ymd'),
            $sequence
        );
    }

    private function extractSequenceLabel(?string $certificateNumber): string
    {
        if (!$certificateNumber) {
            return '0000';
        }

        $parts = explode('-', $certificateNumber);

        return $parts[3] ?? '0000';
    }

    private function courseCode(Course $course): string
    {
        $source = $course->slug ?: $course->title;

        $words = preg_split('/[\s\-_]+/', Str::upper(trim($source))) ?: [];
        $code = '';

        foreach ($words as $word) {
            $word = preg_replace('/[^A-Z0-9]/', '', $word);
            if ($word !== '') {
                $code .= substr($word, 0, 1);
            }
        }

        return $code !== '' ? $code : 'CRS';
    }

    private function formatDateLong(Carbon $date): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $date->format('d') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
    }

    private function placeholderImageBase64(): string
    {
        $path = public_path('images/logo.png');

        if (!file_exists($path)) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    private function buildFrontSummary(Course $course, User $user, int $totalTopics): array
    {
        return [
            'participant_name' => $user->name,
            'course_title' => $course->title,
            'program_title' => $course->studyProgram?->title ?? '-',
            'total_topics' => $totalTopics,
        ];
    }
}