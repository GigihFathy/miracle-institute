<x-emails.layouts.learning title="Sertifikat Siap Diunduh">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo {{ $certificate->user->full_name ?? 'Student' }},</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Selamat! Sertifikat digital untuk course <strong>{{ $course->title }}</strong> sudah tersedia.</p>

    <x-emails.components.button :url="$url">Unduh Sertifikat</x-emails.components.button>

    <p style="margin:0;font-size:14px;line-height:1.8;color:#475569;">Sertifikat ini diterbitkan setelah seluruh prasyarat course terpenuhi.</p>
</x-emails.layouts.learning>