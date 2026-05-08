<?php

namespace App\Providers;

use App\Events\AssessmentPassed;
use App\Events\CertificateGenerated;
use App\Events\TopicCompleted;
use App\Events\EnrollmentCreated;
use App\Listeners\HandleAssessmentPassed;
use App\Listeners\IssueCertificatesForCompletedTopic;
use App\Listeners\SendCertificateIssuedNotification;
use App\Listeners\SendEnrollmentNotification;
use Illuminate\Support\ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AssessmentPassed::class => [
            HandleAssessmentPassed::class,
        ],

        TopicCompleted::class => [
            IssueCertificatesForCompletedTopic::class,
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

        \App\Events\VideoSessionCreated::class => [
            \App\Listeners\SendVideoSessionCreatedEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
