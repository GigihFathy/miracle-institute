<?php

namespace App\Providers;

use App\Email\Events\AssessmentSubmissionProcessed;
use App\Email\Events\AttendanceIssueDetected as EmailAttendanceIssueDetected;
use App\Email\Events\ContentCompleted;
use App\Email\Events\CourseEnrollmentCreated;
use App\Email\Events\VideoSessionScheduled;
use App\Email\Listeners\ScheduleVideoSessionReminder;
use App\Email\Listeners\SendAssessmentSubmissionNotification;
use App\Email\Listeners\SendAttendanceIssueNotification;
use App\Email\Listeners\SendContentCompletionNotification;
use App\Email\Listeners\SendCourseEnrollmentNotification;
use App\Email\Listeners\SendVideoSessionScheduledNotification;
use App\Events\AssessmentPassed;
use App\Events\CertificateGenerated;
use App\Listeners\HandleAssessmentPassed;
use App\Listeners\SendCertificateIssuedNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CourseEnrollmentCreated::class => [
            SendCourseEnrollmentNotification::class,
        ],

        ContentCompleted::class => [
            SendContentCompletionNotification::class,
        ],

        AssessmentSubmissionProcessed::class => [
            SendAssessmentSubmissionNotification::class,
        ],

        VideoSessionScheduled::class => [
            SendVideoSessionScheduledNotification::class,
            ScheduleVideoSessionReminder::class,
        ],

        EmailAttendanceIssueDetected::class => [
            SendAttendanceIssueNotification::class,
        ],

        AssessmentPassed::class => [
            HandleAssessmentPassed::class,
        ],

        CertificateGenerated::class => [
            SendCertificateIssuedNotification::class,
        ],

        \App\Events\EnrollmentConfirmed::class => [
            \App\Listeners\SendEnrollmentConfirmationEmail::class,
        ],

        \App\Events\TopicCompleted::class => [
            \App\Listeners\SendTopicCompletedEmail::class,
        ],

        \App\Events\CourseCompleted::class => [
            \App\Listeners\SendCourseCompletedEmail::class,
        ],

        \App\Events\AssessmentAvailable::class => [
            \App\Listeners\SendAssessmentAvailableEmail::class,
        ],

        \App\Events\AssessmentSubmitted::class => [
            \App\Listeners\SendAssessmentSubmissionReceiptEmail::class,
        ],

        \App\Events\AttendanceIssueDetected::class => [
            \App\Listeners\SendAttendanceIssueEmail::class,
        ],

        \App\Events\CertificateIssued::class => [
            \App\Listeners\SendCertificateReadyEmail::class,
        ],

        \App\Events\VideoSessionReminderTriggered::class => [
            \App\Listeners\SendVideoSessionReminderEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
