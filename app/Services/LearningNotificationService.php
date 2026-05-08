<?php

namespace App\Services;

use App\Mail\AssessmentSubmittedMail;
use App\Mail\CertificateIssuedMail;
use App\Mail\CourseCompletedMail;
use App\Mail\EnrollmentConfirmedMail;
use App\Mail\SessionReminderMail;
use App\Mail\TopicCompletedMail;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Models\VideoSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class LearningNotificationService
{
    public function sendEnrollmentConfirmed(CourseEnrollment $enrollment): void
    {
        Mail::to($enrollment->user->email)->queue(new EnrollmentConfirmedMail($enrollment));
    }

    public function sendTopicCompleted(TopicProgress $progress): void
    {
        $user = $progress->courseEnrollment->user;
        Mail::to($user->email)->queue(new TopicCompletedMail($progress));
    }

    public function sendCourseCompleted(CourseEnrollment $enrollment): void
    {
        Mail::to($enrollment->user->email)->queue(new CourseCompletedMail($enrollment));
    }

    public function sendAssessmentSubmitted(AssessmentAttempt $attempt): void
    {
        Mail::to($attempt->user->email)->queue(new AssessmentSubmittedMail($attempt));
    }

    public function sendCertificateIssued(Certificate $certificate): void
    {
        Mail::to($certificate->user->email)->queue(new CertificateIssuedMail($certificate));
    }

    public function sendSessionReminder(VideoSession $session, int $userId, string $windowKey): void
    {
        $cacheKey = "session-reminder:{$session->id}:{$userId}:{$windowKey}";

        if (!Cache::add($cacheKey, true, now()->addDays(2))) {
            return; // already sent for this user/window
        }

        $user = $session->topic->course->enrollments()
            ->where('user_id', $userId)
            ->with('user')
            ->first()?->user;

        if (! $user) {
            return;
        }

        Mail::to($user->email)->queue(new SessionReminderMail($session));
    }
}