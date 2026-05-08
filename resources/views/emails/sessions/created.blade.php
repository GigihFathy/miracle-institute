@component('emails.layouts.learning', [
    'title' => 'Sesi Video Baru',
    'accent' => '#7c3aed',
    'accentSoft' => '#f5f3ff',
    'badge' => 'NEW SESSION',
    'icon' => '🎥',
    'heroTitle' => 'Sesi video baru telah tersedia',
    'heroText' => 'Anda terdaftar pada course ini, jadi sesi ini otomatis dikirim ke seluruh mahasiswa enrolled.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, ada sesi video baru yang baru saja dibuat untuk course Anda.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Course', 'value' => $session->topic->course->title])
        @include('emails.partials.meta-row', ['label' => 'Topik', 'value' => $session->topic->name])
        @include('emails.partials.meta-row', ['label' => 'Judul Sesi', 'value' => $session->title])
        @include('emails.partials.meta-row', ['label' => 'Mulai', 'value' => optional($session->start_at)->format('d M Y H:i')])
        @include('emails.partials.meta-row', ['label' => 'Selesai', 'value' => optional($session->end_at)->format('d M Y H:i')])
    </table>

    @component('emails.components.button', ['url' => url('/topics/' . $session->topic->slug), 'color' => '#7c3aed'])
        Buka Materi
    @endcomponent
@endcomponent