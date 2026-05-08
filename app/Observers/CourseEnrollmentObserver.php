<?php

namespace App\Observers;

use App\Models\CourseEnrollment;
use App\Services\LearningNotificationService;

class CourseEnrollmentObserver
{
    public function __construct(protected LearningNotificationService $notifier) {}

    public function created(CourseEnrollment $enrollment): void
    {
        $enrollment->loadMissing(['user', 'course.studyProgram']);
        $this->notifier->sendEnrollmentConfirmed($enrollment);
    }
}