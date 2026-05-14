<?php

namespace App\Notifications;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Assessment $assessment
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Assessment Tersedia')
            ->view('emails.assessments.available', [
                'notifiable' => $notifiable,
                'assessment' => $this->assessment,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'assessment_id' => $this->assessment->id,
            'message' => 'Assessment baru telah tersedia untuk Anda kerjakan.'
        ];
    }
}