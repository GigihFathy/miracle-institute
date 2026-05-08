<x-emails.layouts.learning title="Kendala Kehadiran">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo {{ $attendance->user->full_name ?? 'Student' }},</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Terdapat kendala pada presensi sesi <strong>{{ $attendance->videoSession->title }}</strong>.</p>

    <div style="margin-top:18px;padding:18px;border:1px solid #fecaca;background:#fef2f2;border-radius:16px;color:#991b1b;line-height:1.8;font-size:14px;">
        {{ $messageBody }}
    </div>
</x-emails.layouts.learning>