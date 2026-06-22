<section class="mentor-workspace-panel">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="mentor-workspace-heading">
                {{ __('mentor.topic_tabs.attendances.title') }}
            </h2>

            <p class="mentor-workspace-subheading">
                {{ __('mentor.topic_tabs.attendances.subtitle') }}
            </p>
        </div>

        {{-- @if($canManageAttendance)
            <span class="inline-flex items-center rounded-full border border-slate-200 bg-[var(--mentor-primary-soft)] px-3 py-1 text-xs font-medium text-[var(--mentor-primary)]">
                {{ __('mentor.topic_tabs.attendances.manager_badge') }}
            </span>
        @endif --}}
    </div>

    <div class="mentor-workspace-toolbar mt-5">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            class="mentor-workspace-field sm:max-w-sm"
            placeholder="{{ __('mentor.topic_tabs.attendances.filters.search_placeholder') }}"
        >

        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
            <select
                wire:model.live="statusFilter"
                class="mentor-workspace-field sm:w-40"
            >
                <option value="">{{ __('mentor.topic_tabs.attendances.filters.all_status') }}</option>
                <option value="present">{{ __('mentor.topic_tabs.attendances.status.present') }}</option>
                <option value="late">{{ __('mentor.topic_tabs.attendances.status.late') }}</option>
                <option value="online">{{ __('mentor.topic_tabs.attendances.status.online') }}</option>
            </select>

            <select
                wire:model.live="perPage"
                class="mentor-workspace-field sm:w-36"
            >
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="15">15 / page</option>
                <option value="25">25 / page</option>
            </select>
        </div>
    </div>

    <div class="mentor-workspace-table mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[var(--mentor-primary)]">
                    <tr class="text-left">
                        <th class="px-5 py-4 font-bold text-white">{{ __('mentor.topic_tabs.attendances.table.session') }}</th>
                        <th class="px-5 py-4 font-bold text-white">{{ __('mentor.topic_tabs.attendances.table.student') }}</th>
                        <th class="px-5 py-4 font-bold text-white">{{ __('mentor.topic_tabs.attendances.table.status') }}</th>
                        <th class="px-5 py-4 font-bold text-white">{{ __('mentor.topic_tabs.attendances.table.check_in') }}</th>
                        <th class="px-5 py-4 font-bold text-white">{{ __('mentor.topic_tabs.attendances.table.check_out') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($attendances as $attendance)
                        <tr class="transition hover:bg-[var(--mentor-primary-soft-2)]">
                            <td class="px-5 py-4">
                                <div class="font-medium text-[var(--mentor-primary)]">
                                    {{ $attendance->videoSession?->title ?? '-' }}
                                </div>
                                <div class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                    {{ $attendance->videoSession?->start_at?->format('d M Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-[var(--mentor-primary)]">
                                    {{ $attendance->user?->name ?? '-' }}
                                </div>
                                <div class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                    {{ $attendance->user?->email ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-[var(--mentor-primary)]">
                                    {{ __('mentor.topic_tabs.attendances.status.' . $attendance->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[var(--mentor-primary)]">
                                {{ $attendance->check_in_at?->format('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-[var(--mentor-primary)]">
                                {{ $attendance->clock_out_at?->format('d M Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-[var(--mentor-primary)]">
                                        {{ __('mentor.topic_tabs.attendances.empty.title') }}
                                    </div>

                                    <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                        {{ __('mentor.topic_tabs.attendances.empty.description') }}
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
