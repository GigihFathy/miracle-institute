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

    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-200 sm:max-w-sm"
            placeholder="Search student..."
        >

        <select
            wire:model.live="perPage"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-200 sm:w-36"
        >
            <option value="5">5 / page</option>
            <option value="10">10 / page</option>
            <option value="15">15 / page</option>
            <option value="25">25 / page</option>
        </select>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-5 py-4 font-medium text-slate-600">Student</th>
                        <th class="px-5 py-4 font-medium text-slate-600">Status</th>
                        <th class="px-5 py-4 font-medium text-slate-600">Check In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($attendances as $attendance)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $attendance->user?->name ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-slate-700">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ $attendance->check_in_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-14">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-slate-700">
                                        Attendance belum tersedia
                                    </div>

                                    <p class="mt-2 text-sm text-slate-500">
                                        Session aktif belum dibuat atau belum ada data kehadiran student.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($attendances->hasPages())
        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    @endif
</section>