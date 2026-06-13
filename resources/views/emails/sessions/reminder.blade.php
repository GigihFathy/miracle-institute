@component('emails.layouts.learning', [
    'title' => 'Pengingat Sesi Pertemuan',
])
    <p style="margin:0 0 16px 0;">
        Halo <strong>{{ $notifiable->name }}</strong>,
    </p>

    <p style="margin:0 0 16px 0;">
        Sesi pertemuan berikut akan dimulai dalam 3 hari.
    </p>

    <p style="margin:0 0 6px 0;"><strong>Kursus:</strong> {{ $session->topic->course->title }}</p>
    <p style="margin:0 0 6px 0;"><strong>Topik:</strong> {{ $session->topic->name }}</p>
    <p style="margin:0 0 6px 0;"><strong>Sesi:</strong> {{ $session->title }}</p>
    <p style="margin:0 0 16px 0;"><strong>Mulai:</strong> {{ optional($session->start_at)->format('d M Y H:i') }}</p>

    <p style="margin:0 0 16px 0;">
        Silakan buka halaman sesi saat waktunya tiba untuk bergabung.
    </p>

    @component('emails.components.button', ['url' => url('/sessions/' . $session->id), 'color' => '#004777'])
        Buka sesi
    @endcomponent
@endcomponent
