@component('emails.layouts.learning', [
    'title' => 'Sertifikat Siap',
    'accent' => '#7c3aed',
    'accentSoft' => '#f5f3ff',
    'badge' => 'CERTIFICATE',
    'icon' => '🏆',
    'heroTitle' => 'Sertifikat digital Anda sudah tersedia',
    'heroText' => 'Selamat, Anda telah menyelesaikan course dan sertifikat siap diunduh.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, pencapaian Anda sudah resmi tercatat.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Nomor Sertifikat', 'value' => $certificate->certificate_number])
        @include('emails.partials.meta-row', ['label' => 'Course', 'value' => $certificate->course->title])
        @include('emails.partials.meta-row', ['label' => 'Issued At', 'value' => optional($certificate->issued_at)->format('d M Y')])
    </table>

    @component('emails.components.button', ['url' => {{ route('certificates.download') }}, 'color' => '#7c3aed'])
        Download Sertifikat
    @endcomponent
@endcomponent