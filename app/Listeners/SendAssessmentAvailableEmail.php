<?php

namespace App\Listeners;

use App\Events\AssessmentAvailable;
use App\Models\Assessment;
use App\Models\User;
use App\Notifications\AssessmentAvailableNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAssessmentAvailableEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(AssessmentAvailable $event): void
    {
        $assessment = Assessment::query()
            ->with([
                'course',
            ])
            ->findOrFail($event->assessmentId);

        $user = User::query()
            ->findOrFail($event->userId);

        $user->notify(
            new AssessmentAvailableNotification($assessment)
        );
    }
}