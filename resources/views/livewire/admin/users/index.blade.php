<div class="space-y-6">
    <x-ui.page-header title="{{ __('admin.users.page_title') }}" subtitle="{{ __('admin.users.page_subtitle') }}" />

    <div class="rounded-2xl border bg-white p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input wire:model.live="search" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.users.search_placeholder') }}">

            <select wire:model.live="roleFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.users.filters.all_roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="sort" class="rounded-xl border px-4 py-2">
                <option value="latest">{{ __('admin.users.sort.latest') }}</option>
                <option value="name_asc">{{ __('admin.users.sort.name_asc') }}</option>
                <option value="name_desc">{{ __('admin.users.sort.name_desc') }}</option>
                <option value="email_asc">{{ __('admin.users.sort.email_asc') }}</option>
                <option value="email_desc">{{ __('admin.users.sort.email_desc') }}</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border bg-white">
        <div class="overflow-x-auto">
            <table class="table-fixed w-full min-w-full text-sm">
                <thead class="admin-table-head text-left text-slate-600">
                    <tr>
                        <th class="w-1/4 px-4 py-3 font-medium">{{ __('admin.users.table.name') }}</th>
                        <th class="w-1/4 px-4 py-3 font-medium">{{ __('admin.users.table.email') }}</th>
                        <th class="w-1/3 px-4 py-3 font-medium">{{ __('admin.users.table.roles') }}</th>
                        <th class="w-1/6 px-4 py-3 text-right font-medium">{{ __('admin.users.table.action') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="align-top transition-colors hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900" style="overflow-wrap:anywhere;">
                                {{ $row->full_name }}
                            </td>

                            <td class="px-4 py-3 text-slate-700" style="overflow-wrap:anywhere;">
                                {{ $row->email }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($row->roles as $role)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">{{ __('admin.users.no_roles') }}</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($row->roles->contains('name', 'student'))
                                        <div class="relative group">
                                            <button
                                                type="button"
                                                wire:click="openStudentRecap('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 transition hover:bg-sky-100"
                                                aria-label="Rekap Student"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 16.5 10 13.25l2.25 2.25L17.25 9.75" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9.75h1.5v1.5" />
                                                </svg>
                                            </button>
                                            <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-sky-200 bg-white px-2 py-1 text-[11px] font-medium text-sky-700 opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                Rekap Student
                                            </span>
                                        </div>
                                    @endif

                                    <div class="relative group">
                                        <a
                                            href="{{ localized_route('admin.users.roles', $row->id) }}"
                                            class="admin-primary-button inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-dark/20 transition"
                                            aria-label="{{ __('admin.users.actions.manage_roles') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 19.5a7.5 7.5 0 0 0-15 0" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 8.25h.008v.008h-.008V8.25Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                            </svg>
                                        </a>
                                        <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                            {{ __('admin.users.actions.manage_roles') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                {{ __('admin.users.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $rows->links() }}</div>

    @if($showStudentRecapModal && $selectedStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <button type="button" class="absolute inset-0" wire:click="closeStudentRecapModal" aria-label="Tutup rekap student"></button>

            <div class="relative z-10 flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b p-5">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Rekap Student</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $selectedStudent->full_name }} · {{ $selectedStudent->email }}
                        </p>
                    </div>

                    <button type="button" wire:click="closeStudentRecapModal" class="rounded-xl border px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50">
                        Tutup
                    </button>
                </div>

                <div class="overflow-y-auto p-5">
                    @if(empty($studentRecapRows))
                        <div class="rounded-xl border border-dashed border-slate-300 px-5 py-8 text-center text-sm text-slate-500">
                            Student ini belum mengikuti course apa pun.
                        </div>
                    @else
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-wide text-slate-500">Total Course</div>
                                <div class="mt-2 text-2xl font-bold text-slate-900">{{ count($studentRecapRows) }}</div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-wide text-slate-500">Topik Selesai</div>
                                <div class="mt-2 text-2xl font-bold text-slate-900">{{ collect($studentRecapRows)->sum('topics_completed') }}</div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-wide text-slate-500">Kehadiran Tercatat</div>
                                <div class="mt-2 text-2xl font-bold text-slate-900">
                                    {{ collect($studentRecapRows)->sum('attendance_present') + collect($studentRecapRows)->sum('attendance_late') + collect($studentRecapRows)->sum('attendance_absent') }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto rounded-2xl border">
                            <table class="min-w-full text-sm">
                                <thead class="admin-table-head text-left text-slate-600">
                                    <tr>
                                        <th class="px-4 py-3 font-medium">Course</th>
                                        <th class="px-4 py-3 font-medium">Status</th>
                                        <th class="px-4 py-3 font-medium">Progress</th>
                                        <th class="px-4 py-3 font-medium">Kehadiran</th>
                                        <th class="px-4 py-3 font-medium">Enroll</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($studentRecapRows as $recap)
                                        <tr class="align-top">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-900">{{ $recap['course_title'] }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                    {{ ucfirst($recap['enrollment_status']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-900">{{ $recap['topics_completed'] }} / {{ $recap['topics_total'] }} topik</div>
                                                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                                    <div class="h-full rounded-full bg-[#004777]" style="width: {{ $recap['progress_percent'] }}%"></div>
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">{{ $recap['progress_percent'] }}%</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-slate-700">Present: {{ $recap['attendance_present'] }}</div>
                                                <div class="text-slate-700">Late: {{ $recap['attendance_late'] }}</div>
                                                <div class="text-slate-700">Online: {{ $recap['attendance_absent'] }}</div>
                                                <div class="mt-1 text-xs text-slate-500">Total sesi: {{ $recap['sessions_total'] }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600">
                                                <div>{{ $recap['enrolled_at'] }}</div>
                                                @if($recap['completed_at'] !== '-')
                                                    <div class="mt-1 text-xs text-slate-500">Selesai: {{ $recap['completed_at'] }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
