<?php

namespace App\Notifications;

use App\Models\VideoSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VideoSessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VideoSession $session
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder Sesi')
            ->view('emails.sessions.reminder', [
                'notifiable' => $notifiable,
                'session' => $this->session,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'video_session_id' => $this->session->id,
            'message' => 'Sesi video baru telah dijadwalkan.',
        ];
    }
}