<section class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-slate-900">
                Students
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Monitoring progress student pada topic ini.
            </p>
        </div>

        @if($canManageStudents)
            <span class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-xs font-medium text-white">
                Student Manager
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
                <thead class="bg-slate-50">
                    <tr class="text-left">
                        <th class="px-5 py-4 font-medium text-slate-600">
                            Student
                        </th>

                        <th class="px-5 py-4 font-medium text-slate-600">
                            Progress
                        </th>

                        <th class="px-5 py-4 font-medium text-slate-600">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($students as $student)
                        @php
                            $status = $student->topicProgresses->first()?->status ?? 'not_started';
                            $percent = match ($status) {
                                'completed' => 100,
                                'in_progress' => 50,
                                default => 0,
                            };
                        @endphp

                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-900">
                                    {{ $student->user?->name }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="w-full max-w-xs">
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div
                                            class="h-full rounded-full bg-slate-900 transition-all"
                                            style="width: {{ $percent }}%"
                                        ></div>
                                    </div>

                                    <div class="mt-2 text-xs font-medium text-slate-500">
                                        {{ $percent }}%
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-slate-700">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-14">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-slate-700">
                                        Belum ada student
                                    </div>

                                    <p class="mt-2 text-sm text-slate-500">
                                        Enrollment student pada topic ini masih kosong.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($students->hasPages())
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    @endif
</section>