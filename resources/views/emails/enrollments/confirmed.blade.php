@component('emails.layouts.learning', [
    'title' => 'Enrollment Berhasil',
    'accent' => '#0f766e',
    'accentSoft' => '#ecfeff',
    'badge' => 'CONFIRMATION',
    'icon' => '✅',
    'heroTitle' => 'Pendaftaran course Anda sudah aktif',
    'heroText' => 'Akun Anda berhasil terdaftar. Anda sudah bisa mulai belajar dan melihat progres pembelajaran.'
])
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding:0 0 14px 0;font-size:15px;line-height:1.8;color:#334155;">
                Halo <strong>{{ $notifiable->name }}</strong>, enrollment Anda berhasil diproses.
            </td>
        </tr>
        <tr>
            <td style="padding:10px 0 18px 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">
                    @include('emails.partials.meta-row', ['label' => 'Course', 'value' => $enrollment->course->title])
                    @include('emails.partials.meta-row', ['label' => 'Tanggal', 'value' => optional($enrollment->enrolled_at)->format('d M Y H:i')])
                    @include('emails.partials.meta-row', ['label' => 'Status', 'value' => ucfirst($enrollment->status)])
                </table>
            </td>
        </tr>
    </table>

    @component('emails.components.button', ['url' => url('/courses/' . $enrollment->course->slug), 'color' => '#0f766e'])
        Mulai Belajar
    @endcomponent

    @slot('footer')
        @include('emails.components.footer')
    @endslot
@endcomponent