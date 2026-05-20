<section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)] sm:p-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-[var(--mentor-primary)]">
                {{ __('mentor.topic_tabs.students.title') }}
            </h2>

            <p class="mt-1 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                {{ __('mentor.topic_tabs.students.subtitle') }}
            </p>
        </div>

        {{-- @if($canManageStudents)
            <span class="inline-flex items-center rounded-full bg-[var(--mentor-primary)] px-3 py-1 text-xs font-medium text-white">
                {{ __('mentor.topic_tabs.students.manager_badge') }}
            </span>
        @endif --}}
    </div>

    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            class="w-full rounded-xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] px-4 py-2 text-sm text-[var(--mentor-primary)] focus:border-[var(--mentor-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--mentor-secondary-solid)] sm:max-w-sm"
            placeholder="Search student..."
        >

        <select
            wire:model.live="perPage"
            class="w-full rounded-xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] px-4 py-2 text-sm text-[var(--mentor-primary)] focus:border-[var(--mentor-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--mentor-secondary-solid)] sm:w-36"
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
                <thead class="bg-[var(--mentor-primary)]">
                    <tr class="text-left">
                        <th class="px-5 py-4 font-bold text-white">
                            {{ __('mentor.topic_tabs.students.table.student') }}
                        </th>

                        <th class="px-5 py-4 font-bold text-white">
                            {{ __('mentor.topic_tabs.students.table.progress') }}
                        </th>

                        <th class="px-5 py-4 font-bold text-white">
                            {{ __('mentor.topic_tabs.students.table.status') }}
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

                        <tr class="transition hover:bg-[var(--mentor-primary-soft-2)]">
                            <td class="px-5 py-4">
                                <div class="font-medium text-[var(--mentor-primary)]">
                                    {{ $student->user?->name }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="w-full max-w-xs">
                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--mentor-primary-soft)]">
                                        <div
                                            class="h-full rounded-full bg-[var(--mentor-primary)] transition-all"
                                            style="width: {{ $percent }}%"
                                        ></div>
                                    </div>

                                    <div class="mt-2 text-xs font-medium text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                        {{ $percent }}%
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-[var(--mentor-primary)]">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-14">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-[var(--mentor-primary)]">
                                        {{ __('mentor.topic_tabs.students.empty.title') }}
                                    </div>

                                    <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                        {{ __('mentor.topic_tabs.students.empty.description') }}
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
