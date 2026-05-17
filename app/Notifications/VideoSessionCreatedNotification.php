<?php

namespace App\Notifications;

use App\Models\VideoSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class VideoSessionCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

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
            ->subject('Sesi Video Baru Telah Dibuka')
            ->view('emails.sessions.created', [
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