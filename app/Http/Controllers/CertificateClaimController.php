<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCertificatePdfJob;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateClaimController extends Controller
{
    public function course(Request $request, Course $course, CertificateService $certificateService)
    {
        $user = $request->user();

        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        abort_unless($enrollment, 403, 'Anda belum enroll course ini.');

        $topicsCompleted = TopicProgress::where('course_enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        abort_unless($topicsCompleted === $course->topics()->count(), 403, 'Selesaikan seluruh topic terlebih dahulu.');

        $assessment = $course->assessment()->where('status', 'active')->first();

        if ($assessment) {
            $passed = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->where('passed', true)
                ->exists();

            abort_unless($passed, 403, 'Lulus assessment terlebih dahulu.');
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('type', 'course')
            ->where('course_id', $course->id)
            ->first();

        if ($certificate && $certificate->file_path) {
            return redirect()->route('certificates.download', $certificate->id);
        }

        $issued = $certificateService->issueCourseCertificateIfEligible($user->id, $course->id);

        if (!$issued) {
            return back()->with('error', 'Certificate belum bisa di-claim karena course belum selesai.');
        }

        GenerateCertificatePdfJob::dispatchSync($issued->id);

        return redirect()->route('certificates.download', $issued->id);
    }

    public function topic(Request $request, Topic $topic, CertificateService $certificateService)
    {
        $user = $request->user();

        $progress = TopicProgress::where('topic_id', $topic->id)
            ->whereHas('courseEnrollment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$progress || $progress->status !== 'completed') {
            return back()->with('error', 'Certificate belum bisa di-claim karena topic belum selesai.');
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('type', 'topic')
            ->where('topic_id', $topic->id)
            ->first();

        if ($certificate && $certificate->file_path) {
            return redirect()->route('certificates.download', $certificate->id);
        }

        $issued = $certificateService->issueTopicCertificate(
            $user->id,
            $topic->course_id,
            $topic->id
        );

        if (!$issued) {
            return back()->with('error', 'Certificate gagal dibuat.');
        }

        GenerateCertificatePdfJob::dispatchSync($issued->id);

        return redirect()->route('certificates.download', $issued->id);
    }

    public function claim(string $courseId)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $course = Course::with('topics')->findOrFail($courseId);

        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return $this->deny('Kamu belum terdaftar pada course ini.');
        }

        $existingCertificate = Certificate::where([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'type' => 'course',
        ])->first();

        if ($existingCertificate) {
            return redirect()->route('certificates.download', $existingCertificate->id)
                ->with('info', 'Sertifikat sudah tersedia.');
        }

        $totalTopics = $course->topics->count();

        $completedTopics = TopicProgress::where('course_enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        if ($totalTopics === 0 || $completedTopics < $totalTopics) {
            return $this->deny(
                "Selesaikan semua topik terlebih dahulu. ({$completedTopics}/{$totalTopics})"
            );
        }

        $assessment = $course->assessment()->first();

        if ($assessment) {
            $passed = DB::table('assessment_attempts')
                ->where('assessment_id', $assessment->id)
                ->where('user_id', $user->id)
                ->where('status', 'submitted')
                ->where('score', '>=', $assessment->passing_grade)
                ->exists();

            if (!$passed) {
                return $this->deny('Kamu harus lulus assessment terlebih dahulu.');
            }
        }

        $certificate = DB::transaction(function () use ($user, $course) {
            return Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'type' => 'course',
                'issued_at' => now(),
            ]);
        });

        return redirect()
            ->route('certificates.download', $certificate->id)
            ->with('success', 'Sertifikat berhasil diklaim.');
    }

    private function deny(string $message)
    {
        return redirect()->back()->with('error', $message);
    }
}