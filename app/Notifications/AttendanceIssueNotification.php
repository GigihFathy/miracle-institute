<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceIssueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Attendance $attendance
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kendala Kehadiran')
            ->view('emails.attendances.issue', [
                'notifiable' => $notifiable,
                'attendance' => $this->attendance,
            ]);
    }
}