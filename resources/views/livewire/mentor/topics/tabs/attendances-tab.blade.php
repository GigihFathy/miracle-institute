<section class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6">
     <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-slate-900">
                Attendances
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Rekap kehadiran berdasarkan session topic.
            </p>
        </div>

        @if($canManageAttendance)
            <span class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-xs font-medium text-white">
                Attendance Manager
            </span>
        @endif
    </div>

    <div class="mt-5 space-y-4">
        @forelse($sessions as $session)
            @php
                $sessionAttendances = $attendancesBySession[$session->id] ?? collect();
            @endphp

            <div class="rounded-2xl border p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ $session->title }}</div>
                        <div class="mt-1 text-xs text-slate-500">
                            {{ $session->start_at?->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="flex gap-2 text-[11px] uppercase tracking-wide">
                        <span class="rounded-full border px-2 py-1">Present {{ $sessionAttendances->where('status', 'present')->count() }}</span>
                        <span class="rounded-full border px-2 py-1">Late {{ $sessionAttendances->where('status', 'late')->count() }}</span>
                        <span class="rounded-full border px-2 py-1">Absent {{ $sessionAttendances->where('status', 'absent')->count() }}</span>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="p-3">Student</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Check In</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessionAttendances as $attendance)
                                <tr class="border-t">
                                    <td class="p-3 font-medium">{{ $attendance->user?->name }}</td>
                                    <td class="p-3">
                                        <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-slate-700">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="p-3">{{ $attendance->check_in_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-slate-500">Belum ada attendance.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                <div class="mx-auto max-w-md">
                    <div class="text-sm font-medium text-slate-700">
                        Attendance belum tersedia
                    </div>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Session aktif belum dibuat atau belum ada data kehadiran student.
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</section>