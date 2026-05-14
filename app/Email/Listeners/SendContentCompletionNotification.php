<?php

namespace App\Email\Listeners;

use App\Email\Events\ContentCompleted;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Notifications\CourseCompletedNotification;
use App\Notifications\TopicCompletedNotification;

class SendContentCompletionNotification 
{
    public function handle(ContentCompleted $event): void
    {
        if ($event->subjectType === 'course_enrollment') {
            $enrollment = CourseEnrollment::with(['user', 'course'])
                ->findOrFail($event->subjectId);

            $enrollment->user->notify(
                new CourseCompletedNotification($enrollment)
            );

            return;
        }

        if ($event->subjectType === 'topic_progress') {
            $progress = TopicProgress::with(['courseEnrollment.user', 'topic'])
                ->findOrFail($event->subjectId);

            $progress->courseEnrollment->user->notify(
                new TopicCompletedNotification($progress)
            );
        }
    }
}