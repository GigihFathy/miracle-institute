<?php

namespace App\Listeners;

use App\Events\AssessmentAvailable;
use App\Models\Assessment;
use App\Models\User;
use App\Notifications\AssessmentAvailableNotification;


class SendAssessmentAvailableEmail
{
    public function handle(AssessmentAvailable $event): void
    {
        $assessment = Assessment::findOrFail($event->assessmentId);

        $user = User::findOrFail($event->userId);

        $user->notify(
            new AssessmentAvailableNotification($assessment)
        );
    }
}