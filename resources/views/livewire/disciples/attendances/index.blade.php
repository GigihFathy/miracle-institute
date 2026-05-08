<div class="space-y-6 lg:px-5 2xl:px-8 pb-10 scale-[0.90] origin-top">

    {{-- HERO --}}
    <section class="rounded-[28px] border bg-white shadow-sm overflow-hidden">
        <div class="p-7 lg:p-10 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-3 max-w-4xl min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Disciples Studio
                    </div>

                    <div class="flex flex-wrap items-center gap-3 min-w-0">
                        <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                            Attendance Studio
                        </h1>

                        <a href="{{ route('mentor.sessions.index') }}"
                           class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                            Kembali
                        </a>
                    </div>

                    <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                        Kelola presensi peserta per video session dengan clock-in dan clock-out yang tervalidasi otomatis.
                    </p>

                    <div class="flex flex-wrap gap-2 text-[11px]">
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                            Clock-in ≤ 45 menit setelah sesi dimulai
                        </span>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                            Clock-out ≤ 15 menit sebelum sesi berakhir
                        </span>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                            Student scoped
                        </span>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Attendance
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                @foreach($statsCards as $card)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 min-w-0">
                        <div class="text-xs text-slate-500">
                            {{ $card['label'] }}
                        </div>

                        <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900 break-words">
                            {{ $card['value'] }}
                        </div>

                        <div class="mt-1 text-xs leading-5 text-slate-500 break-words">
                            {{ $card['note'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            <input wire:model.live.debounce.300ms="search"
                   type="search"
                   class="w-full border rounded-xl px-4 py-3"
                   placeholder="Search user/session...">

            <select wire:model.live="courseFilter" class="border rounded-xl px-4 py-3">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model.live="topicFilter" class="border rounded-xl px-4 py-3">
                <option value="">All topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">
                        {{ $topic->course?->title }} · {{ $topic->name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="sessionFilter" class="border rounded-xl px-4 py-3">
                <option value="">All sessions</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->topic?->course?->title }} · {{ $session->topic?->name }} · {{ $session->title }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-3">
                <option value="">All status</option>
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
            </select>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- LIST --}}
        <div class="space-y-4 min-w-0">
            @forelse($rows as $row)
                @php
                    $toneClass = $row->timing_badge_class ?? 'bg-slate-100 text-slate-600 border-slate-200';
                    $statusClass = $row->status_badge_class ?? 'bg-slate-100 text-slate-600 border-slate-200';
                @endphp

                <div wire:key="attendance-{{ $row->id }}"
                     class="rounded-[26px] border bg-white shadow-sm overflow-hidden min-w-0">

                    <button type="button"
                            wire:click="selectAttendance('{{ $row->id }}')"
                            class="w-full text-left p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="space-y-2 min-w-0">
                                <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400 truncate">
                                    {{ $row->videoSession?->topic?->course?->title }}
                                </div>

                                <h3 class="text-xl font-semibold text-slate-900 break-words">
                                    {{ $row->videoSession?->title }}
                                </h3>

                                <p class="text-sm text-slate-500 break-words">
                                    {{ $row->videoSession?->topic?->name }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs border {{ $statusClass }}">
                                    {{ ucfirst($row->status) }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs border {{ $toneClass }}">
                                    {{ $row->timing_label }}
                                </span>
                            </div>
                        </div>
                    </button>

                    <div class="px-5 pb-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Student</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $row->user?->full_name }}
                                </div>
                                <div class="text-xs text-slate-500 break-words">
                                    {{ $row->user?->email }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Check In</div>
                                <div class="font-semibold mt-1">
                                    {{ $row->check_in_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Check Out</div>
                                <div class="font-semibold mt-1">
                                    {{ $row->clock_out_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Session Time</div>
                                <div class="font-semibold mt-1">
                                    {{ $row->videoSession?->start_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    to {{ $row->videoSession?->end_at?->format('H:i') ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            <div class="rounded-2xl border bg-white p-4">
                                <div class="text-xs text-slate-500">Course</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $row->videoSession?->topic?->course?->title }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-white p-4">
                                <div class="text-xs text-slate-500">Topic</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $row->videoSession?->topic?->name }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-white p-4">
                                <div class="text-xs text-slate-500">Platform</div>
                                <div class="font-semibold mt-1">
                                    {{ $row->platform_label }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 text-xs text-slate-500 leading-6 break-words">
                            {{ $row->window_label }}
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button wire:click.stop="edit('{{ $row->id }}')"
                                    class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-medium">
                                Edit
                            </button>

                            <button wire:click.stop="setStatus('{{ $row->id }}', 'present')"
                                    wire:loading.attr="disabled"
                                    wire:target="setStatus"
                                    class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-medium disabled:opacity-60">
                                Present
                            </button>

                            <button wire:click.stop="setStatus('{{ $row->id }}', 'late')"
                                    wire:loading.attr="disabled"
                                    wire:target="setStatus"
                                    class="px-3 py-2 rounded-xl bg-amber-50 text-amber-700 text-xs font-medium disabled:opacity-60">
                                Late
                            </button>

                            <button wire:click.stop="setStatus('{{ $row->id }}', 'absent')"
                                    wire:loading.attr="disabled"
                                    wire:target="setStatus"
                                    class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium disabled:opacity-60">
                                Absent
                            </button>

                            <button wire:click.stop="delete('{{ $row->id }}')"
                                    class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-medium">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.empty-state
                    title="No attendance records"
                    description="Data presensi belum tersedia."
                />
            @endforelse

            <div>
                {{ $rows->links() }}
            </div>
        </div>

        {{-- SIDEBAR --}}
        <aside class="rounded-[28px] bg-white border shadow-sm p-6 space-y-5 sticky top-24 h-fit min-w-0">
            <div class="flex items-start justify-between gap-4 min-w-0">
                <div class="min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                        Selected Attendance
                    </div>

                    <h2 class="text-2xl font-bold mt-2 leading-tight break-words">
                        {{ $selectedAttendance?->videoSession?->title ?? 'No record selected' }}
                    </h2>
                </div>

                @if($selectedAttendance)
                    <span class="px-3 py-1 rounded-full text-xs border {{ $selectedAttendance->status_badge_class }}">
                        {{ ucfirst($selectedAttendance->status) }}
                    </span>
                @endif
            </div>

            @if($selectedAttendance)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Course</div>
                        <div class="font-semibold mt-1 break-words">
                            {{ $selectedAttendance->videoSession?->topic?->course?->title }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Topic</div>
                        <div class="font-semibold mt-1 break-words">
                            {{ $selectedAttendance->videoSession?->topic?->name }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Student</div>
                        <div class="font-semibold mt-1 break-words">
                            {{ $selectedAttendance->user?->full_name }}
                        </div>
                        <div class="text-xs text-slate-500 break-words">
                            {{ $selectedAttendance->user?->email }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Platform</div>
                        <div class="font-semibold mt-1">
                            {{ $selectedAttendance->platform_label }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Check In</div>
                        <div class="font-semibold mt-1">
                            {{ $selectedAttendance->check_in_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Check Out</div>
                        <div class="font-semibold mt-1">
                            {{ $selectedAttendance->clock_out_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4 space-y-2">
                    <div class="text-sm font-semibold">Timing</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs border {{ $selectedAttendance->timing_badge_class }}">
                            {{ $selectedAttendance->timing_label }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs border bg-slate-100 text-slate-600 border-slate-200">
                            {{ $selectedAttendance->window_label }}
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4 space-y-2">
                    <div class="text-sm font-semibold">IP Address</div>
                    <div class="text-sm text-slate-600 break-words">
                        {{ $selectedAttendance->ip_address ?? '-' }}
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                    <div class="text-sm font-semibold">Quick actions</div>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="edit('{{ $selectedAttendance->id }}')"
                                class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs text-center">
                            Edit
                        </button>

                        <a href="{{ route('topics.show', $selectedAttendance->videoSession?->topic?->slug) }}"
                           class="px-3 py-2 rounded-xl border text-xs text-center">
                            Public topic
                        </a>
                    </div>
                </div>
            @else
                <x-ui.empty-state
                    title="Select a record"
                    description="Klik salah satu attendance card untuk melihat detail operasional."
                />
            @endif
        </aside>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="showModal"
        :title="$editingId ? 'Edit Attendance' : 'New Attendance'"
        description="Form manual untuk input presensi yang tervalidasi otomatis."
        maxWidth="max-w-3xl"
    >
        <form wire:submit.prevent="save" class="space-y-4">
            <select wire:model="video_session_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select session</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->topic?->course?->title }} · {{ $session->topic?->name }} · {{ $session->title }}
                    </option>
                @endforeach
            </select>
            @error('video_session_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <select wire:model="user_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select student</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->full_name }} · {{ $user->email }}
                    </option>
                @endforeach
            </select>
            @error('user_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
            </select>
            @error('status') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-slate-500">Check in at</label>
                    <input wire:model="check_in_at" type="datetime-local" class="w-full border rounded-xl px-4 py-3">
                    @error('check_in_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-slate-500">Clock out at</label>
                    <input wire:model="clock_out_at" type="datetime-local" class="w-full border rounded-xl px-4 py-3">
                    @error('clock_out_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-xs text-slate-500">IP Address</label>
                <input wire:model="ip_address" type="text" class="w-full border rounded-xl px-4 py-3" placeholder="127.0.0.1">
                @error('ip_address') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4 text-sm text-slate-600 leading-6">
                Status akan divalidasi otomatis dari waktu check-in dan clock-out. Jika waktu melewati batas sesi, data akan ditolak agar integritas presensi tetap terjaga.
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <button type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-xl border">
                    Cancel
                </button>

                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Save Attendance</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>