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
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengingat Sesi Pertemuan')
            ->view('emails.sessions.reminder', [
                'notifiable' => $notifiable,
                'session' => $this->session,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'video_session_id' => $this->session->id,
            'message' => 'Sesi pertemuan akan dimulai dalam 2 hari.',
        ];
    }
}
