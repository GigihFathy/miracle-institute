@component('emails.layouts.learning', [
    'title' => 'Kendala Kehadiran',
    'accent' => '#be123c',
    'accentSoft' => '#fff1f2',
    'badge' => 'ATTENDANCE ISSUE',
    'icon' => '⚠️',
    'heroTitle' => 'Ada isu pada presensi Anda',
    'heroText' => 'Tim atau sistem mendeteksi status kehadiran yang perlu ditinjau kembali.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, berikut informasi presensi Anda:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Pertemuan', 'value' => $attendance->videoSession->title])
        @include('emails.partials.meta-row', ['label' => 'Status', 'value' => ucfirst($attendance->status)])
        @include('emails.partials.meta-row', ['label' => 'Check-in', 'value' => $attendance->check_in_at ? $attendance->check_in_at->format('d M Y H:i') : '-'])
    </table>

    @component('emails.components.button', ['url' => url('/attendance/' . $attendance->id), 'color' => '#be123c'])
        Tinjau Presensi
    @endcomponent
@endcomponent