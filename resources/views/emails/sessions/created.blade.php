@component('emails.layouts.learning', [
    'title' => 'Pertemuan Baru',
])
    <p style="margin:0 0 16px 0;">
        Halo <strong>{{ $notifiable->name }}</strong>,
    </p>

    <p style="margin:0 0 16px 0;">
        Ada pertemuan baru yang baru saja dibuat untuk topik pembelajaran Anda.
    </p>

    <p style="margin:0 0 6px 0;"><strong>Topik pembelajaran:</strong> {{ $session->topic->course->title }}</p>
    <p style="margin:0 0 6px 0;"><strong>Sesi:</strong> {{ $session->topic->name }}</p>
    <p style="margin:0 0 6px 0;"><strong>Judul pertemuan:</strong> {{ $session->title }}</p>
    <p style="margin:0 0 6px 0;"><strong>Mulai:</strong> {{ optional($session->start_at)->format('d M Y H:i') }}</p>
    <p style="margin:0 0 16px 0;"><strong>Selesai:</strong> {{ optional($session->end_at)->format('d M Y H:i') }}</p>

    @component('emails.components.button', ['url' => url('/topics/' . $session->topic->slug), 'color' => '#004777'])
        Buka pertemuan
    @endcomponent
@endcomponent
