<x-emails.layouts.learning title="Topik Selesai">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo {{ $topicProgress->courseEnrollment->user->full_name ?? 'Student' }},</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Anda telah menyelesaikan topik <strong>{{ $topic->name }}</strong> pada course <strong>{{ $course->title }}</strong>.</p>

    <x-emails.components.button :url="$url">Lihat Topik</x-emails.components.button>

    <p style="margin:0;font-size:14px;line-height:1.8;color:#475569;">Lanjutkan ke topik berikutnya untuk menjaga ritme belajar.</p>
</x-emails.layouts.learning>