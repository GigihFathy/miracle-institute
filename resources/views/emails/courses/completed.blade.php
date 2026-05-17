@component('emails.layouts.learning', [
    'title' => 'Course Berhasil Diselesaikan',
    'accent' => '#15803d',
    'accentSoft' => '#f0fdf4',
    'badge' => 'COURSE COMPLETED',
    'icon' => '🎓',
    'heroTitle' => 'Selamat, seluruh course telah selesai',
    'heroText' => 'Progress pembelajaran Anda telah mencapai 100%. Sertifikat digital akan tersedia setelah proses validasi selesai.'
])

    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>,
        Anda telah berhasil menyelesaikan seluruh materi pembelajaran pada course berikut.
    </p>

    <table role="presentation"
           width="100%"
           cellpadding="0"
           cellspacing="0"
           style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:24px;">

        @include('emails.partials.meta-row', [
            'label' => 'Course',
            'value' => $enrollment->course->title
        ])

        @include('emails.partials.meta-row', [
            'label' => 'Status',
            'value' => ucfirst($enrollment->status)
        ])

        @include('emails.partials.meta-row', [
            'label' => 'Tanggal Selesai',
            'value' => optional($enrollment->completed_at)->format('d M Y H:i')
        ])

    </table>

    <table role="presentation"
           width="100%"
           cellpadding="0"
           cellspacing="0"
           style="margin-bottom:24px;background:#f8fafc;border-radius:18px;border:1px solid #e2e8f0;">

        <tr>
            <td style="padding:22px;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:10px;">
                    Ringkasan Pencapaian
                </div>

                <div style="font-size:14px;line-height:1.8;color:#475569;">
                    Anda telah menyelesaikan seluruh topik pembelajaran yang tersedia pada course ini.
                    Progress ini akan tercatat permanen pada histori pembelajaran akun Anda.
                </div>
            </td>
        </tr>

    </table>

    @component('emails.components.button', [
        'url' => url('/courses/' . $enrollment->course->slug),
        'color' => '#15803d'
    ])
        Lihat Course
    @endcomponent

    <div style="margin-top:26px;font-size:13px;line-height:1.8;color:#64748b;">
        Jika course ini menyediakan sertifikat digital, sistem akan mengirimkan email lanjutan ketika sertifikat siap diunduh.
    </div>

    @slot('footer')
        @include('emails.components.footer')
    @endslot

@endcomponent