@component('emails.layouts.learning', [
    'title' => 'Pengingat Pertemuan',
])
    <p style="margin:0 0 16px 0;">
        Halo <strong>{{ $notifiable->name }}</strong>,
    </p>

    <p style="margin:0 0 16px 0;">
        Pertemuan berikut akan dimulai dalam 2 hari.
    </p>

    <p style="margin:0 0 6px 0;"><strong>Topik pembelajaran:</strong> {{ $session->topic->course->title }}</p>
    <p style="margin:0 0 6px 0;"><strong>Sesi:</strong> {{ $session->topic->name }}</p>
    <p style="margin:0 0 6px 0;"><strong>Pertemuan:</strong> {{ $session->title }}</p>
    <p style="margin:0 0 16px 0;"><strong>Mulai:</strong> {{ optional($session->start_at)->format('d M Y H:i') }}</p>

    <p style="margin:0 0 16px 0;">
        Silakan buka halaman pertemuan saat waktunya tiba untuk bergabung.
    </p>

    @component('emails.components.button', ['url' => localized_route('courses.show', [
        'slug' => $session->topic->course->slug,
        'tab' => 'topics',
        'topic' => $session->topic->id,
        'session' => $session->id,
    ]), 'color' => '#004777'])
        Buka pertemuan
    @endcomponent
@endcomponent
