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
                        <th class="w-[22%] px-4 py-3 font-medium">{{ __('admin.users.table.name') }}</th>
                        <th class="w-[22%] px-4 py-3 font-medium">{{ __('admin.users.table.email') }}</th>
                        <th class="w-[28%] px-4 py-3 font-medium">{{ __('admin.users.table.roles') }}</th>
                        <th class="w-[12%] px-4 py-3 font-medium">{{ __('admin.users.table.status') }}</th>
                        <th class="w-[16%] px-4 py-3 text-right font-medium">{{ __('admin.users.table.action') }}</th>
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

                            <td class="px-4 py-3">
                                @if($row->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                        {{ __('admin.users.status.active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600 ring-1 ring-red-200">
                                        {{ __('admin.users.status.inactive') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($row->roles->contains('name', 'student'))
                                        <div class="relative group">
                                            <button
                                                type="button"
                                                wire:click="openStudentRecap('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 transition hover:bg-sky-100"
                                                aria-label="Informasi Murid"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 16.5 10 13.25l2.25 2.25L17.25 9.75" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9.75h1.5v1.5" />
                                                </svg>
                                            </button>
                                            <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-sky-200 bg-white px-2 py-1 text-[11px] font-medium text-sky-700 opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                Informasi Murid
                                            </span>
                                        </div>
                                    @endif

                                    @if($row->id !== auth()->id())
                                        <div class="relative group">
                                            <button
                                                type="button"
                                                wire:click="toggleActive('{{ $row->id }}')"
                                                wire:confirm="{{ $row->is_active ? __('admin.users.actions.deactivate') . ' ' . $row->full_name . '?' : __('admin.users.actions.activate') . ' ' . $row->full_name . '?' }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border transition {{ $row->is_active ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                                aria-label="{{ $row->is_active ? __('admin.users.actions.deactivate') : __('admin.users.actions.activate') }}"
                                            >
                                                @if($row->is_active)
                                                    {{-- Nonaktifkan: icon ban/block --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    {{-- Aktifkan: icon check-circle --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                @endif
                                            </button>
                                            <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border bg-white px-2 py-1 text-[11px] font-medium opacity-0 transition group-hover:opacity-100 {{ $row->is_active ? 'border-red-200 text-red-600' : 'border-emerald-200 text-emerald-700' }}">
                                                {{ $row->is_active ? __('admin.users.actions.deactivate') : __('admin.users.actions.activate') }}
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
            <button type="button" class="absolute inset-0" wire:click="closeStudentRecapModal" aria-label="Tutup informasi murid"></button>

            <div class="relative z-10 flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                {{-- Header --}}
                <div class="flex items-center justify-between gap-4 border-b px-5 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Informasi Murid</h2>
                    <button type="button" wire:click="closeStudentRecapModal" class="rounded-xl border px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50">
                        Tutup
                    </button>
                </div>

                <div class="overflow-y-auto">
                    {{-- Profile Section --}}
                    @php
                        $isMale = in_array(strtolower((string) $selectedStudent->gender), ['male', 'laki-laki', 'l', 'm']);
                        $isFemale = in_array(strtolower((string) $selectedStudent->gender), ['female', 'perempuan', 'p', 'f']);
                        $genderLabel = $isMale ? 'Laki-laki' : ($isFemale ? 'Perempuan' : ucfirst((string) $selectedStudent->gender));
                        $age = $selectedStudent->dob ? $selectedStudent->dob->age : null;
                    @endphp
                    <div class="border-b px-5 py-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            {{-- Left: labeled info --}}
                            <div class="grid grid-cols-1 gap-y-2.5 gap-x-8 text-sm sm:grid-cols-4">
                                <div>
                                    <div class="text-xs text-slate-400">Nama</div>
                                    <div class="mt-0.5 font-medium text-slate-900">{{ $selectedStudent->full_name }}</div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Email</div>
                                    <div class="mt-0.5 text-slate-700">{{ $selectedStudent->email }}</div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Gender</div>
                                    <div class="mt-0.5">
                                        @if($selectedStudent->gender)
                                            @if($isMale)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-600 ring-1 ring-sky-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M15 2h5a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0V5.414l-4.293 4.293a6 6 0 1 1-1.414-1.414L17.586 4H15a1 1 0 1 1 0-2ZM9 9a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>
                                                    Laki-laki
                                                </span>
                                            @elseif($isFemale)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-pink-50 px-2 py-0.5 text-xs font-medium text-pink-500 ring-1 ring-pink-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a6 6 0 1 1 0 12A6 6 0 0 1 12 2Zm0 14a1 1 0 0 1 1 1v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-1h-1a1 1 0 1 1 0-2h1v-1a1 1 0 0 1 1-1Z"/></svg>
                                                    Perempuan
                                                </span>
                                            @else
                                                <span class="text-slate-700">{{ $genderLabel }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Tanggal Lahir</div>
                                    <div class="mt-0.5 text-slate-700">
                                        @if($selectedStudent->dob)
                                            {{ $selectedStudent->dob->format('d M Y') }} <span class="text-slate-400">({{ $age }} tahun)</span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">No. Telepon</div>
                                    <div class="mt-0.5 text-slate-700">{{ $selectedStudent->phone ?: '—' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Bergabung</div>
                                    <div class="mt-0.5 text-slate-700">{{ $selectedStudent->created_at->format('d M Y') }}</div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Verifikasi Email</div>
                                    <div class="mt-0.5">
                                        @if($selectedStudent->hasVerifiedEmail())
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                Terverifikasi · {{ $selectedStudent->email_verified_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                                Belum terverifikasi
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-400">Status & Role</div>
                                    <div class="mt-0.5 flex flex-wrap gap-1">
                                        @if($selectedStudent->is_active)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">Aktif</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-600 ring-1 ring-red-200">Nonaktif</span>
                                        @endif
                                        @foreach($selectedStudent->roles as $role)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Right: verify button --}}
                            @if(!$selectedStudent->hasVerifiedEmail())
                                <div class="shrink-0">
                                    <button
                                        type="button"
                                        wire:click="verifyUser('{{ $selectedStudent->id }}')"
                                        wire:confirm="Verifikasi email {{ $selectedStudent->full_name }}?"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-600"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        Verifikasi Email
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recap Section --}}
                    <div class="p-5">
                        @if(empty($studentRecapRows))
                            <div class="rounded-xl border border-dashed border-slate-300 px-5 py-8 text-center text-sm text-slate-500">
                                Murid ini belum mengikuti topik pembelajaran apa pun.
                            </div>
                        @else
                            <div class="grid gap-4 md:grid-cols-3">
                                <div class="rounded-2xl border bg-slate-50 p-4">
                                    <div class="text-xs uppercase tracking-wide text-slate-500">Total Topik pembelajaran</div>
                                    <div class="mt-2 text-2xl font-bold text-slate-900">{{ count($studentRecapRows) }}</div>
                                </div>

                                <div class="rounded-2xl border bg-slate-50 p-4">
                                    <div class="text-xs uppercase tracking-wide text-slate-500">Sesi Selesai</div>
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
                                            <th class="px-4 py-3 font-medium">Topik pembelajaran</th>
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
                                                    <div class="font-medium text-slate-900">{{ $recap['topics_completed'] }} / {{ $recap['topics_total'] }} sesi</div>
                                                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                                        <div class="h-full rounded-full bg-[#004777]" style="width: {{ $recap['progress_percent'] }}%"></div>
                                                    </div>
                                                    <div class="mt-1 text-xs text-slate-500">{{ $recap['progress_percent'] }}%</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-slate-700">Hadir: {{ $recap['attendance_present'] }}</div>
                                                    <div class="text-slate-700">Terlambat: {{ $recap['attendance_late'] }}</div>
                                                    <div class="text-slate-700">Online: {{ $recap['attendance_absent'] }}</div>
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
        </div>
    @endif
</div>
