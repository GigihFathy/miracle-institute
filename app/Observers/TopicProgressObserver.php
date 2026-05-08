<?php

namespace App\Observers;

use App\Mail\CourseCompletedMail;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Services\LearningNotificationService;

class TopicProgressObserver
{
    public function __construct(protected LearningNotificationService $notifier) {}

    public function created(TopicProgress $progress): void
    {
        $this->handleProgress($progress, null);
    }

    public function updated(TopicProgress $progress): void
    {
        $originalStatus = $progress->getOriginal('status');
        $this->handleProgress($progress, $originalStatus);
    }

    protected function handleProgress(TopicProgress $progress, ?string $originalStatus): void
    {
        $progress->loadMissing(['courseEnrollment.user', 'courseEnrollment.course.topics', 'topic']);

        if ($progress->status === 'completed' && $originalStatus !== 'completed') {
            $this->notifier->sendTopicCompleted($progress);
        }

        // Course completion gate: only when all topics are completed.
        $enrollment = $progress->courseEnrollment;
        if (! $this->isCourseCompleted($enrollment)) {
            return;
        }

        // Send course completion once only.
        $alreadyIssued = Certificate::where('course_id', $enrollment->course_id)
            ->where('user_id', $enrollment->user_id)
            ->exists();

        if (! $alreadyIssued) {
            $this->notifier->sendCourseCompleted($enrollment);
        }
    }

    protected function isCourseCompleted(CourseEnrollment $enrollment): bool
    {
        $totalTopics = $enrollment->course->topics()->count();
        if ($totalTopics === 0) {
            return false;
        }

        $completedTopics = TopicProgress::where('course_enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        return $completedTopics >= $totalTopics;
    }
}