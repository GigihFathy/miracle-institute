<?php

namespace App\Notifications;

use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CourseEnrollment $enrollment
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Enrollment Berhasil')
            ->view('emails.enrollments.confirmed', [
                'notifiable' => $notifiable,
                'enrollment' => $this->enrollment,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'course_id' => $this->enrollment->course_id,
            'message' => 'Pendaftaran course kamu telah berhasil dikonfirmasi.'
        ];
    }
}