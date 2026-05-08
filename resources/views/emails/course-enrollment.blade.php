<x-mail::message>
# Enrollment Berhasil

Halo {{ $user->full_name }},

Kamu berhasil terdaftar di course **{{ $course->title }}**.

<x-mail::button :url="$url">
Buka Course
</x-mail::button>

Jika ada kendala akses, silakan cek akun atau hubungi admin.

Terima kasih,  
{{ config('app.name') }}
</x-mail::message>