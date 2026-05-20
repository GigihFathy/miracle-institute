<?php

namespace App\Providers;


// FOR CORE EVENTS & LISTENERS

use App\Events\AssessmentPassed;
use App\Events\AssessmentAvailable;

use App\Listeners\HandleAssessmentPassed;
use App\Listeners\SendAssessmentAvailableEmail;

// FOR MAIL NOTIFICATIONS

use App\Email\Events\AssessmentSubmissionProcessed;
use App\Email\Events\AttendanceIssueDetected;
use App\Email\Events\ContentCompleted;
use App\Email\Events\CourseEnrollmentCreated;
use App\Email\Events\VideoSessionScheduled;
use App\Email\Events\CertificateIssued;

use App\Email\Listeners\ScheduleVideoSessionReminder;
use App\Email\Listeners\SendAssessmentSubmissionNotification;
use App\Email\Listeners\SendAttendanceIssueNotification;
use App\Email\Listeners\SendContentCompletionNotification;
use App\Email\Listeners\SendCourseEnrollmentNotification;
use App\Email\Listeners\SendVideoSessionScheduledNotification;
use App\Email\Listeners\SendCertificateIssuedNotification;


use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        // Core events and listeners
        AssessmentPassed::class => [
            HandleAssessmentPassed::class,
        ],

        \App\Events\VideoSessionReminderTriggered::class => [
            \App\Listeners\SendVideoSessionReminderEmail::class,
        ],
        

        // For email notifications
        CourseEnrollmentCreated::class => [
            SendCourseEnrollmentNotification::class,
        ],

        ContentCompleted::class => [
            SendContentCompletionNotification::class,
        ],

        AssessmentAvailable::class => [
            SendAssessmentAvailableEmail::class,
        ],

        VideoSessionScheduled::class => [
            SendVideoSessionScheduledNotification::class,
            ScheduleVideoSessionReminder::class,
        ],

        AttendanceIssueDetected::class => [
            SendAttendanceIssueNotification::class,
        ],


        AssessmentSubmissionProcessed::class => [
            SendAssessmentSubmissionNotification::class,
        ],


        CertificateIssued::class => [
            SendCertificateIssuedNotification::class,
        ],

        
    ];

    public function boot(): void
    {
        //
    }
}
