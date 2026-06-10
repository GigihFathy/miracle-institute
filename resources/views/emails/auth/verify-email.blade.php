@component('emails.layouts.learning', [
    'title' => 'Verifikasi Akun',
    'accent' => '#004777',
    'accentSoft' => '#eef8ff',
    'badge' => 'VERIFIKASI AKUN',
    'icon' => 'VK',
    'heroTitle' => 'Aktifkan akun Anda untuk mulai belajar',
    'heroText' => 'Satu langkah lagi untuk mengakses materi, sesi, dan fitur belajar di platform.',
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, terima kasih sudah mendaftar di {{ config('app.name') }}.
        Silakan verifikasi alamat email Anda terlebih dahulu agar akun bisa digunakan sepenuhnya.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px 0;border:1px solid #d7e7f7;border-radius:18px;background:#f8fbff;">
        <tr>
            <td style="padding:18px 20px;font-size:14px;line-height:1.75;color:#475569;">
                Setelah verifikasi berhasil, Anda bisa masuk ke dashboard, mengikuti kursus, dan menerima notifikasi pembelajaran dengan normal.
            </td>
        </tr>
    </table>

    @component('emails.components.button', ['url' => $verificationUrl, 'color' => '#004777'])
        Verifikasi Sekarang
    @endcomponent

    <p style="margin:18px 0 0 0;font-size:13px;line-height:1.75;color:#64748b;">
        Jika tombol tidak berfungsi, salin dan buka tautan ini di browser Anda:
    </p>
    <p style="margin:8px 0 0 0;font-size:13px;line-height:1.75;word-break:break-all;color:#004777;">
        <a href="{{ $verificationUrl }}" style="color:#004777;text-decoration:underline;">{{ $verificationUrl }}</a>
    </p>
@endcomponent
