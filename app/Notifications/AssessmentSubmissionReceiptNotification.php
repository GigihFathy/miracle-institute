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
        return ['mail', 'database'];
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

    public function toArray($notifiable): array
    {
        return [
            'attempt_id' => $this->attempt->id,
            'assessment_id' => $this->attempt->assessment_id,
            'passed' => $this->attempt->passed,
            'message' => 'Jawaban assessment kamu berhasil diterima dan sedang diproses.',
        ];
    }
}