<x-emails.layouts.learning title="Course Selesai">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">
        Halo {{ $user->full_name ?? $user->name }},
    </p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">
        Seluruh topic pada course <strong>{{ $course->title }}</strong> telah selesai.
    </p>
    <x-emails.components.button :url="$url">Buka Course</x-emails.components.button>
</x-emails.layouts.learning>