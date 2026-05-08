<div class="space-y-6 lg:px-5 2xl:px-8 pb-10 scale-[0.90] origin-top">

    {{-- HERO --}}
    <section class="rounded-[28px] border bg-white shadow-sm overflow-hidden">
        <div class="p-7 lg:p-10 space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-3 max-w-4xl min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Disciples Studio
                    </div>

                    <div class="space-y-3 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                                Session Studio
                            </h1>

                            <a href="{{ route('mentor.courses.index') }}"
                               class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                            Kelola sesi video dengan struktur yang jelas, terhubung ke topic dan course, lengkap dengan indikator platform serta relevansi ke topik utama.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Session
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            @php
                $statRows = collect($statsCards)->chunk(3);
            @endphp

            <div class="space-y-3">
                @foreach($statRows as $row)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ $row->count() === 3 ? 'xl:grid-cols-3' : ($row->count() === 2 ? 'xl:grid-cols-2' : 'xl:grid-cols-1') }}">
                        @foreach($row as $card)
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
                @endforeach
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
            <input wire:model.live.debounce.300ms="search"
                   type="search"
                   class="w-full border rounded-xl px-4 py-3"
                   placeholder="Search session...">

            <select wire:model.live="courseFilter" class="border rounded-xl px-4 py-3">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model.live="topicFilter" class="border rounded-xl px-4 py-3">
                <option value="">All topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->course?->title }} · {{ $topic->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-3">
                <option value="">All status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- SESSION GRID --}}
        <div class="space-y-5 min-w-0">
            @if($rows->isEmpty())
                <x-ui.empty-state
                    title="No sessions found"
                    description="Belum ada sesi yang cocok dengan filter saat ini."
                />
            @else
                <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-5">
                    @foreach($rows as $row)
                        <div
                            wire:key="session-{{ $row->id }}"
                            class="rounded-[26px] border bg-white shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden min-w-0">

                            <button type="button"
                                    wire:click="selectSession('{{ $row->id }}')"
                                    class="w-full text-left block">
                                <div class="p-5 space-y-4">
                                    <div class="flex items-start justify-between gap-3 min-w-0">
                                        <div class="min-w-0">
                                            <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400 truncate">
                                                {{ $row->topic?->course?->title }}
                                            </div>

                                            <div class="text-xl font-semibold leading-tight break-words line-clamp-2">
                                                {{ $row->title }}
                                            </div>
                                        </div>

                                        <span class="shrink-0 px-2.5 py-1 rounded-full text-[11px] bg-slate-100 border border-slate-200">
                                            {{ ucfirst($row->status) }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] border {{ $row->platform_badge_class }}">
                                            {{ $row->platform_label }}
                                        </span>

                                        <span class="px-2.5 py-1 rounded-full text-[11px] border {{ $row->relevance_badge_class }}">
                                            {{ $row->relevance_label }}
                                        </span>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="text-sm text-slate-600 break-words">
                                            {{ $row->topic?->name }}
                                        </div>

                                        <div class="text-xs text-slate-500 space-y-1">
                                            <div>{{ $row->schedule_text }}</div>
                                            <div>Duration: {{ $row->duration_text }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Attendances</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->attendance_total }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Platform</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->platform_label }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </button>

                            <div class="border-t p-5 bg-slate-50 space-y-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('mentor.attendances.index', ['sessionFilter' => $row->id]) }}"
                                       class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-medium">
                                        Attendances
                                    </a>

                                    <a href="{{ route('topics.show', $row->topic?->slug) }}"
                                       class="px-3 py-2 rounded-xl border text-xs">
                                        Public topic
                                    </a>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button wire:click.stop="edit('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-medium">
                                        Edit
                                    </button>

                                    <button wire:click.stop="delete('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4 sticky top-24 h-fit min-w-0">
            <div class="rounded-[28px] bg-white border shadow-sm overflow-hidden">
                @if($selectedSession)
                    <div class="p-6 space-y-5 min-w-0">
                        <div class="space-y-2">
                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                                Selected Session
                            </div>

                            <h2 class="text-2xl font-bold tracking-tight text-slate-900 leading-tight break-words">
                                {{ $selectedSession->title }}
                            </h2>

                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedSession->topic?->course?->title }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedSession->topic?->name }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ ucfirst($selectedSession->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Platform</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedSession->platform_label }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Duration</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedSession->duration_text }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Start</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedSession->start_at?->format('d M Y, H:i') }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">End</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedSession->end_at?->format('d M Y, H:i') }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Attendances</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedSession->attendance_total }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Attendance rate</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedSession->attendance_rate }}%
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-2">
                            <div class="text-sm font-semibold">Relevance</div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] border {{ $selectedSession->relevance_badge_class }}">
                                {{ $selectedSession->relevance_label }}
                            </span>
                            <p class="text-xs text-slate-500 leading-6">
                                Sesi ini terhubung langsung ke topic utama dan menjadi bagian dari struktur course.
                            </p>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Links</div>

                            <div class="space-y-2 text-sm break-words">
                                <div>
                                    <div class="text-xs text-slate-500">Meeting Link</div>
                                    <a href="{{ $selectedSession->zoom_link }}" target="_blank" class="text-slate-900 underline break-all">
                                        {{ $selectedSession->zoom_link }}
                                    </a>
                                </div>

                                @if($selectedSession->record_link)
                                    <div>
                                        <div class="text-xs text-slate-500">Record Link</div>
                                        <a href="{{ $selectedSession->record_link }}" target="_blank" class="text-slate-900 underline break-all">
                                            {{ $selectedSession->record_link }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Quick actions</div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('mentor.attendances.index', ['sessionFilter' => $selectedSession->id]) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs text-center">
                                    Attendances
                                </a>

                                <a href="{{ route('topics.show', $selectedSession->topic?->slug) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Public topic
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-10">
                        <x-ui.empty-state
                            title="Select a session"
                            description="Klik salah satu session card untuk melihat detail dan shortcut manajemen."
                        />
                    </div>
                @endif
            </div>
        </aside>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="showModal"
        :title="$editingId ? 'Edit Session' : 'New Session'"
        description="Studio form untuk video session."
        maxWidth="max-w-3xl"
    >
        <form wire:submit.prevent="save" class="space-y-4">
            <select wire:model="topic_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select topic</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->course?->title }} · {{ $topic->name }}</option>
                @endforeach
            </select>
            @error('topic_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <input wire:model="title" class="w-full border rounded-xl px-4 py-3" placeholder="Session title">
            @error('title') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <input wire:model="zoom_link" class="w-full border rounded-xl px-4 py-3" placeholder="Zoom / meeting link">
            @error('zoom_link') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <input wire:model="record_link" class="w-full border rounded-xl px-4 py-3" placeholder="Record link (optional)">
            @error('record_link') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-slate-500">Start at</label>
                    <input wire:model="start_at" type="datetime-local" class="w-full border rounded-xl px-4 py-3">
                    @error('start_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-slate-500">End at</label>
                    <input wire:model="end_at" type="datetime-local" class="w-full border rounded-xl px-4 py-3">
                    @error('end_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="rounded-2xl border bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">Platform</div>
                    <div class="mt-1 font-semibold text-slate-900">
                        Auto-derived from meeting link
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">Relevance</div>
                    <div class="mt-1 font-semibold text-slate-900">
                        Must stay linked to a topic
                    </div>
                </div>
            </div>

            <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            @error('status') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
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
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>