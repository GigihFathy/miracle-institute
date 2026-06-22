@component('emails.layouts.learning', [
    'title' => 'Verifikasi Akun',
])
    <p style="margin:0 0 16px 0;">
        Halo <strong>{{ $notifiable->name }}</strong>,
    </p>

    <p style="margin:0 0 16px 0;">
        Terima kasih sudah mendaftar di {{ config('app.name') }}. Silakan verifikasi alamat email Anda terlebih dahulu agar akun bisa digunakan sepenuhnya.
    </p>

    <p style="margin:0 0 16px 0;">
        Setelah verifikasi berhasil, Anda bisa masuk ke dashboard, mengikuti topik pembelajaran, dan menerima reminder pertemuan dengan normal.
    </p>

    @component('emails.components.button', ['url' => $verificationUrl, 'color' => '#004777'])
        Verifikasi email
    @endcomponent

    <p style="margin:16px 0 0 0;font-size:13px;line-height:1.75;color:#6b7280;">
        Jika tautan di atas tidak bisa dibuka, gunakan link berikut:
    </p>
    <p style="margin:8px 0 0 0;font-size:13px;line-height:1.75;word-break:break-all;">
        <a href="{{ $verificationUrl }}" style="color:#004777;text-decoration:underline;">{{ $verificationUrl }}</a>
    </p>
@endcomponent
