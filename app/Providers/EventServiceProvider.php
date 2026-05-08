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
        EnrollmentCreated::class => [
            SendEnrollmentNotification::class,
        ],

        \App\Events\EnrollmentConfirmed::class => [
            \App\Listeners\SendEnrollmentConfirmedMail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
