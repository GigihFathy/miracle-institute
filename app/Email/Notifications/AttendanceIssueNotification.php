<?php

namespace App\Email\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceIssueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Attendance $attendance,
        public string $issueType
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->issueType === 'late_join'
            ? 'Reminder: Anda Bergabung Terlambat di Sesi'
            : 'Peringatan: Absen dari Sesi';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.attendances.issue', [
                'notifiable' => $notifiable,
                'attendance' => $this->attendance,
                'issueType' => $this->issueType,
            ]);
    }
}
