<?php

namespace App\Notifications;

use App\Models\AssessmentAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentSubmissionReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AssessmentAttempt $attempt
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Jawaban Assessment Diterima')
            ->view('emails.assessments.submitted', [
                'notifiable' => $notifiable,
                'attempt' => $this->attempt,
            ]);
    }
}