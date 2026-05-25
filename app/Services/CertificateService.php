<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AssessmentAttempt;
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
            $course = Course::query()
                ->whereKey($course->id)
                ->lockForUpdate()
                ->firstOrFail();

            $enrollment = CourseEnrollment::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = Certificate::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === 'issued') {
                $this->ensureCourseCertificateNumberFormat($existing, $course);
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

            $recipientSequence = Certificate::query()
                ->where('course_id', $course->id)
                ->where('status', 'issued')
                ->lockForUpdate()
                ->count() + 1;

            $certificateNumber = $this->generateCertificateNumber($course, $recipientSequence);
            $issuedAt = now();

            $certificate = $existing ?? new Certificate();

            $certificate->forceFill([
                'certificate_number' => $certificateNumber,
                'user_id' => $user->id,
                'course_id' => $course->id,
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
        $this->ensureDompdfFontDirectory();

        if ($certificate->course_id && !$certificate->topic_id) {
            $certificate = DB::transaction(function () use ($certificate) {
                $lockedCertificate = Certificate::query()
                    ->whereKey($certificate->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $course = Course::query()
                    ->whereKey($lockedCertificate->course_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->ensureCourseCertificateNumberFormat($lockedCertificate, $course);

                return $lockedCertificate->fresh();
            });
        }

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
            'certificate' => $certificate,
            'course' => $course,
            'user' => $user,
            'issuedAt' => $issuedAt,
            'frontDate' => $this->formatDateLong($issuedAt),
            'sequenceLabel' => $this->extractSequenceLabel($certificate->certificate_number),
            'frontSummary' => $this->buildFrontSummary($course, $user, $topics->count()),
            'backTopics' => $this->buildBackTopics($course, $user),
            'achievementSummary' => $this->buildAchievementSummary($course, $user, $topics->count()),
            'logoBase64' => $this->placeholderImageBase64(),
            'background' => null,
            'backgroundBack' => null,
            'signature' => null,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function ensureDompdfFontDirectory(): void
    {
        $fontPath = storage_path('fonts');

        if (!is_dir($fontPath)) {
            mkdir($fontPath, 0755, true);
        }
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

        $sessionIds = DB::table('video_sessions')
            ->join('topics', 'topics.id', '=', 'video_sessions.topic_id')
            ->where('topics.course_id', $course->id)
            ->pluck('video_sessions.id');

        $presentFull = $sessionIds->isEmpty()
            ? 0
            : Attendance::query()
                ->where('attendances.user_id', $user->id)
                ->whereIn('attendances.video_session_id', $sessionIds)
                ->where('attendances.status', 'present')
                ->count();

        $late = $sessionIds->isEmpty()
            ? 0
            : Attendance::query()
                ->where('attendances.user_id', $user->id)
                ->whereIn('attendances.video_session_id', $sessionIds)
                ->where('attendances.status', 'late')
                ->count();

        $absent = $sessionIds->isEmpty()
            ? 0
            : Attendance::query()
                ->where('attendances.user_id', $user->id)
                ->whereIn('attendances.video_session_id', $sessionIds)
                ->where('attendances.status', 'absent')
                ->count();

        $assessmentAttempt = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('assessment', fn ($query) => $query->where('course_id', $course->id))
            ->whereNotNull('score')
            ->orderByDesc('passed')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->first();

        return [
            'topics_total' => $totalTopics,
            'topics_completed' => $completedTopics,
            'assessment_score' => $assessmentAttempt?->score,
            'assessment_passed' => (bool) ($assessmentAttempt?->passed ?? false),
            'attendance_sessions_total' => $sessionIds->count(),
            'attendance_present_full' => $presentFull,
            'attendance_checked_in' => $presentFull + $late,
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

    private function generateCertificateNumber(Course $course, int $recipientSequence): string
    {
        $courseNumber = str_pad((string) ($course->certificate_course_number ?: 1), 3, '0', STR_PAD_LEFT);
        $prefixCode = $course->certificate_prefix_code ?: $this->courseCode($course);

        return sprintf(
            '%s-%s/%s/%s/%s',
            str_pad((string) $recipientSequence, 5, '0', STR_PAD_LEFT),
            $courseNumber,
            Str::upper($prefixCode),
            now()->format('m'),
            now()->format('Y')
        );
    }

    private function extractSequenceLabel(?string $certificateNumber): string
    {
        if (!$certificateNumber) {
            return '00000';
        }

        return Str::before($certificateNumber, '-') ?: '00000';
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

    private function ensureCourseCertificateNumberFormat(Certificate $certificate, Course $course): void
    {
        if ($this->hasNewCourseCertificateNumberFormat($certificate->certificate_number)) {
            return;
        }

        $recipientSequence = Certificate::query()
            ->where('course_id', $course->id)
            ->where('status', 'issued')
            ->where(function ($query) use ($certificate) {
                $issuedAt = $certificate->issued_at ?? $certificate->created_at ?? now();

                $query->where('issued_at', '<', $issuedAt)
                    ->orWhere(function ($inner) use ($issuedAt, $certificate) {
                        $inner->where('issued_at', $issuedAt)
                            ->where('id', '<=', $certificate->id);
                    });
            })
            ->count();

        $certificate->forceFill([
            'certificate_number' => $this->generateCertificateNumber($course, max(1, $recipientSequence)),
        ])->save();
    }

    private function hasNewCourseCertificateNumberFormat(?string $certificateNumber): bool
    {
        if (!$certificateNumber) {
            return false;
        }

        return preg_match('/^\d{5}-\d{3}\/[A-Z0-9]+\/\d{2}\/\d{4}$/', $certificateNumber) === 1;
    }

    private function formatDateLong(Carbon $date): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
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
