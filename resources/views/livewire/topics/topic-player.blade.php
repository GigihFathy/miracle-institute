@php
    use Carbon\Carbon;

    $isStudent = auth()->check() && session('active_role') === 'student';
@endphp

<div class="space-y-6 lg:px-36 pb-10">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 space-y-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3 min-w-0">
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    {{ $topic->course?->title }}
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold">{{ $topic->name }}</h1>
                    <p class="text-slate-600 max-w-3xl leading-7">{{ $topic->description }}</p>
                </div>

                @if($canOpenMentorWorkspace)
                    <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                       class="inline-flex px-4 py-2 rounded-xl border text-sm hover:bg-slate-50 transition">
                        Open Mentor Workspace
                    </a>
                @endif
            </div>

            <div class="flex flex-col gap-2 items-start lg:items-end shrink-0">
                @if($isMentor)
                    <span class="px-3 py-1 rounded-full text-xs bg-slate-50 text-slate-600 border border-slate-200">
                        MENTOR REVIEW MODE
                    </span>
                @elseif($topicCompleted)
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                        TOPIC COMPLETED
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs bg-slate-50 text-slate-600 border border-slate-200">
                        {{ strtoupper($topicStatus ?? 'not_started') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Materials</div>
                <div class="text-2xl font-bold mt-1">{{ $topic->materials->count() }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Attendance Records</div>
                <div class="text-2xl font-bold mt-1">{{ $attendanceStats['checked_in'] }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Sessions</div>
                <div class="text-2xl font-bold mt-1">
                    {{ $topic->videoSessions->count() > 0 ? strtoupper($topic->videoSessions->first()->status) : 'NOT AVAILABLE' }}
                </div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Progress</div>
                <div class="text-2xl font-bold mt-1">
                    @if($isMentor)
                        REVIEW
                    @else
                        {{ strtoupper($topicStatus ?? 'not_started') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 justify-between items-center">
            <div class="flex flex-wrap gap-2">
                <button wire:click="setTab('materials')"
                        class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'materials' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:bg-slate-50' }}">
                    Materials
                </button>
                <button wire:click="setTab('sessions')"
                        class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'sessions' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:bg-slate-50' }}">
                    Sessions
                </button>
            </div>

            @if($canStudentInteract && !$topicCompleted && $activeMaterial && $isStudent)
                @php
                    $isLocked = !$hasSessionEnded;
                @endphp

                <button wire:click="{{ $isLocked ? '' : 'markViewed' }}"
                        {{ $isLocked ? 'disabled' : '' }}
                        class="group relative px-6 py-2 rounded-xl font-semibold border transition-all duration-300 flex items-center gap-2
                        {{ $isLocked
                            ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-80'
                            : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 hover:shadow-emerald-200/50 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0'
                        }}">
                    @if($isLocked)
                        <svg xmlns="http://www.w3.org" class="h-4 w-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Attend session to complete</span>
                    @else
                        <svg xmlns="http://www.w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Selesaikan Unit</span>
                    @endif

                    @if($isLocked)
                        <span class="absolute -top-10 left-1/2 -translate-x-1/2 scale-0 transition-all rounded bg-gray-800 p-2 text-xs text-white group-hover:scale-100 whitespace-nowrap">
                            Tombol akan aktif setelah sesi berakhir
                        </span>
                    @endif
                </button>
            @elseif($isMentor)
                <span class="px-4 py-2 rounded-xl border bg-slate-50 text-xs text-slate-500">
                    Read-only review
                </span>
            @endif
        </div>
    </section>

    @if($activeTab === 'materials')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">Material Library</h2>
                        <p class="text-sm text-slate-500">
                            Semua materi dalam topik ini dikumpulkan di satu tempat.
                        </p>
                    </div>
                </div>

                @forelse($topic->materials as $material)
                    @if($loop->first)
                        <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">
                    @endif

                    <button wire:click="selectMaterial('{{ $material->id }}')"
                            class="shrink-0 w-[280px] text-left rounded-2xl border p-5 transition snap-start
                            {{ $activeMaterial?->id === $material->id ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:border-slate-400' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold">{{ $material->name }}</div>
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $activeMaterial?->id === $material->id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600' }}">
                                {{ strtoupper($material->type) }}
                            </span>
                        </div>
                    </button>

                    @if($loop->last)
                        </div>
                    @endif
                @empty
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Materi sedang dipersiapkan</div>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Mentor belum mengunggah materi untuk topik ini. Tab ini tetap aktif agar mahasiswa bisa memantau pembaruan kapan saja.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $activeMaterial?->name ?? 'Select a material' }}</h2>
                        <p class="text-sm text-slate-500">Type: {{ $activeMaterial?->type ?? '-' }}</p>
                    </div>
                </div>

                @if($activeMaterial && $materialUrl)
                    @if($activeMaterial->type === 'video')
                        <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100">
                            <iframe src="{{ $materialUrl }}" class="w-full h-full" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="rounded-2xl border p-5 bg-slate-50">
                            <a href="{{ $materialUrl }}" target="_blank" class="text-slate-900 underline">
                                Open / download material
                            </a>
                        </div>
                    @endif
                @elseif($hasMaterials)
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Pilih materi untuk melihat detail</div>
                        <p class="text-sm text-slate-600 mt-1">
                            Klik salah satu card materi di atas untuk membuka preview atau file terkait.
                        </p>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Belum ada materi tersedia</div>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Mentor belum menyiapkan materi untuk topik ini. Silakan kembali nanti atau cek tab Sessions untuk informasi jadwal belajar.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if($activeTab === 'sessions')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Sessions</h2>
                        <p class="text-sm text-slate-500">
                            Cek jadwal sesi dan join hanya saat window sesi aktif.
                        </p>
                    </div>
                </div>
            </div>

            @forelse($topic->videoSessions as $session)
                @php
                    $attendance = $sessionAttendances->get($session->id);
                    $phase = $this->sessionPhase($session);
                    $buttonText = $this->sessionButtonText($session);
                    $buttonClass = $this->sessionButtonClass($session);
                    $badgeClass = $this->sessionBadgeClass($session);
                    $countdownText = $this->sessionCountdownLabel($session);
                    $startIso = $session->start_at?->toIso8601String();
                    $endIso = $session->end_at?->toIso8601String();
                @endphp

                <div
                    x-data="sessionJoinCard({
                        startAt: @js($startIso),
                        endAt: @js($endIso),
                        initialPhase: @js($phase),
                        title: @js($session->title),
                        startLabel: @js($session->start_at?->format('d M Y, H:i') ?? '-'),
                        endLabel: @js($session->end_at?->format('H:i') ?? '-')
                    })"
                    class="rounded-2xl bg-white border p-5 space-y-4 shadow-sm"
                >
                    <div class="space-y-1">
                        <div class="font-semibold">{{ $session->title }}</div>
                        <div class="text-sm text-slate-500">
                            {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }} - {{ $session->end_at?->format('H:i') ?? '-' }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm">
                            Status: <span class="font-medium" x-text="stateLabel"></span>
                        </div>

                        @if($attendance)
                            <span class="text-xs px-2 py-1 rounded-full {{ $attendanceBadgeClass ?? 'bg-slate-100' }}">
                                {{ strtoupper($attendance->status) }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500" x-text="countdownLabel">
                        {{ $countdownText }}
                    </div>

                    @if($attendance)
                        <div class="space-y-1 text-sm">
                            <div>Check in: {{ $attendance->check_in_at?->format('d M Y, H:i') ?? '-' }}</div>
                            <div>Check out: {{ $attendance->clock_out_at?->format('d M Y, H:i') ?? '-' }}</div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        @if($isStudent)
                            <button
                                type="button"
                                x-on:click="openModal()"
                                :disabled="!canJoin"
                                class="px-4 py-2 rounded-xl border text-sm font-medium transition"
                                :class="buttonClass"
                            >
                                <span x-text="buttonText">{{ $buttonText }}</span>
                            </button>
                        @else
                            <span class="px-4 py-2 rounded-xl border bg-slate-50 text-xs text-slate-500">
                                Read-only review
                            </span>
                        @endif
                    </div>

                    <div
                        x-cloak
                        x-show="open"
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
                        x-on:keydown.escape.window="closeModal()"
                        x-on:click.self="closeModal()"
                    >
                        <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl max-h-[92vh] overflow-y-auto">
                            <div class="flex items-start justify-between gap-4 border-b px-5 py-4 sm:px-6 sm:py-5">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Join Session</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Student akan dicatat ke attendance sebelum diarahkan ke meeting.
                                    </p>
                                </div>

                                <button type="button"
                                        x-on:click="closeModal()"
                                        class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                    Close
                                </button>
                            </div>

                            <div class="px-5 py-5 sm:px-6 sm:py-6 space-y-4">
                                <div class="grid gap-3 sm:grid-cols-2 text-sm">
                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">Title</div>
                                        <div class="mt-1 font-semibold text-slate-900">{{ $session->title }}</div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">Status</div>
                                        <div class="mt-1">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border"
                                                :class="badgeClass"
                                                x-text="stateLabel"></span>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">Start</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">End</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $session->end_at?->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <div class="text-xs uppercase tracking-wide text-slate-500">Countdown</div>
                                    <div class="mt-1 text-base font-semibold text-slate-900" x-text="countdownLabel"></div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                    Clock-in deadline: {{ $session->start_at?->copy()->addMinutes(45)?->format('d M Y, H:i') ?? '-' }}
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t pt-4">
                                    <button
                                        type="button"
                                        x-on:click="closeModal()"
                                        class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"
                                    >
                                        Cancel
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="joinSession('{{ $session->id }}')"
                                        wire:loading.attr="disabled"
                                        @disabled(! $phase === 'live')
                                        class="rounded-xl px-4 py-2 text-sm font-medium transition"
                                        :class="buttonClass"
                                    >
                                        Join &amp; Log Attendance
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                    <div class="font-semibold text-slate-900">Belum ada sesi terjadwal</div>
                    <p class="text-sm text-slate-600 mt-1 leading-6">
                        Mentor belum menjadwalkan sesi untuk topik ini.
                    </p>
                </div>
            @endforelse
        </section>
    @endif
</div>
@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sessionJoinCard', (cfg) => ({
                open: false,
                now: Date.now(),
                timer: null,

                startAt: cfg.startAt ? new Date(cfg.startAt).getTime() : null,
                endAt: cfg.endAt ? new Date(cfg.endAt).getTime() : null,
                initialPhase: cfg.initialPhase ?? 'invalid',

                init() {
                    this.timer = setInterval(() => {
                        this.now = Date.now();
                    }, 1000);
                },

                destroy() {
                    if (this.timer) {
                        clearInterval(this.timer);
                    }
                },

                get phase() {
                    if (!this.startAt || !this.endAt) {
                        return 'invalid';
                    }

                    if (this.now < this.startAt) {
                        return 'upcoming';
                    }

                    if (this.now <= this.endAt) {
                        return 'live';
                    }

                    return 'ended';
                },

                get canJoin() {
                    return this.phase === 'live';
                },

                get stateLabel() {
                    if (this.phase === 'upcoming') return 'Scheduled';
                    if (this.phase === 'live') return 'Live';
                    if (this.phase === 'ended') return 'Completed';

                    return 'Unavailable';
                },

                get badgeClass() {
                    if (this.phase === 'upcoming') return 'bg-amber-100 text-amber-700 border-amber-200';
                    if (this.phase === 'live') return 'bg-emerald-100 text-emerald-700 border-emerald-200';
                    if (this.phase === 'ended') return 'bg-slate-100 text-slate-700 border-slate-200';

                    return 'bg-slate-100 text-slate-700 border-slate-200';
                },

                get buttonClass() {
                    if (this.phase === 'upcoming') {
                        return 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed';
                    }

                    if (this.phase === 'live') {
                        return 'bg-slate-900 text-white border-slate-900 hover:bg-slate-700';
                    }

                    return 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed';
                },

                get buttonText() {
                    if (this.phase === 'upcoming') return 'Not Started';
                    if (this.phase === 'live') return 'Join Session';
                    if (this.phase === 'ended') return 'Completed';

                    return 'Unavailable';
                },

                get countdownLabel() {
                    if (!this.startAt || !this.endAt) {
                        return 'Session schedule belum lengkap.';
                    }

                    let target = this.phase === 'upcoming' ? this.startAt : this.endAt;

                    if (this.phase === 'ended') {
                        return 'Session completed';
                    }

                    let diff = Math.max(0, Math.floor((target - this.now) / 1000));
                    let h = Math.floor(diff / 3600);
                    let m = Math.floor((diff % 3600) / 60);
                    let s = diff % 60;

                    let parts = [];
                    if (h > 0) parts.push(`${h}h`);
                    if (m > 0 || h > 0) parts.push(`${m}m`);
                    parts.push(`${s}s`);

                    return this.phase === 'upcoming'
                        ? `Starts in ${parts.join(' ')}`
                        : `Ends in ${parts.join(' ')}`;
                },

                openModal() {
                    if (this.canJoin) {
                        this.open = true;
                    }
                },

                closeModal() {
                    this.open = false;
                },
            }));
        });
    </script>
@endonce