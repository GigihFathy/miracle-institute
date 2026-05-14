@php
    $isStudent = auth()->check() && session('active_role') === 'student';
@endphp

<div class="space-y-6 lg:px-36 pb-10">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 space-y-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3 min-w-0">
                <div class="text-xs uppercase tracking-wide text-[#004777]/60">
                    {{ $topic->course?->title }}
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#004777]">
                        {{ $topic->name }}
                    </h1>

                    <p class="text-[#004777]/75 max-w-3xl leading-7">
                        {{ $topic->description }}
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">Materials</div>
                <div class="text-2xl font-bold mt-1 text-[#004777]">
                    {{ $materials->count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">Attendance Records</div>
                <div class="text-2xl font-bold mt-1 text-[#004777]">
                    {{ $attendanceStats['checked_in'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">Sessions</div>
                <div class="text-2xl font-bold mt-1 text-[#004777]">
                    {{ $topic->videoSessions->count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">Progress</div>
                <div class="text-2xl font-bold mt-1 text-[#004777]">
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
                <button
                    type="button"
                    wire:click="setTab('materials')"
                    class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'materials' ? 'bg-[#004777] text-white border-[#004777]' : 'bg-white text-[#004777] hover:bg-[#35A7FF]/10 border-[#35A7FF]/30' }}"
                >
                    Materials
                </button>

                <button
                    type="button"
                    wire:click="setTab('sessions')"
                    class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'sessions' ? 'bg-[#004777] text-white border-[#004777]' : 'bg-white text-[#004777] hover:bg-[#35A7FF]/10 border-[#35A7FF]/30' }}"
                >
                    Sessions
                </button>
            </div>

            @if($canStudentInteract &&!$topicCompleted && $activeMaterial && $isStudent)
                @php
                    $isLocked =!$hasSessionEnded;
                @endphp

                <button
                    type="button"
                    wire:click="markViewed"
                    wire:loading.attr="disabled"
                    wire:target="markViewed"
                    @disabled($isLocked)
                    class="group relative px-6 py-2 rounded-xl font-semibold border transition-all duration-300 flex items-center gap-2
                    {{ $isLocked
                        ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-80'
                        : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 hover:shadow-emerald-200/50 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0'
                    }}"
                >
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
            @endif
        </div>
    </section>

    @if($activeTab === 'materials')
        <section
            class="space-y-4"
            x-data="{ openMaterialCompleteModal: false }"
            x-on:material-complete-done.window="openMaterialCompleteModal = false"
        >
            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">Material Library</h2>
                        <p class="text-sm text-slate-500">
                            Semua materi dalam topik ini dikumpulkan di satu tempat.
                        </p>
                    </div>

                    <div wire:loading wire:target="selectMaterial" class="text-xs text-slate-500">
                        Memuat materi...
                    </div>
                </div>

                @forelse($materials as $material)
                    @if($loop->first)
                        <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">
                    @endif

                    @php
                        $card = $materialCards[(string) $material->id] ?? null;
                        $isActive = $activeMaterial?->id === $material->id;
                        $thumbnailUrl = $card['thumbnail_url'] ?? null;
                        $isVideo = $card['is_video'] ?? false;
                    @endphp

                    <button
                        type="button"
                        wire:key="material-card-{{ $material->id }}"
                        wire:click="selectMaterial('{{ $material->id }}')"
                        wire:loading.attr="disabled"
                        wire:target="selectMaterial"
                        class="shrink-0 w-[280px] overflow-hidden text-left rounded-2xl border transition snap-start disabled:opacity-70
                        {{ $isActive ? 'bg-[#004777] text-white border-[#004777] shadow-sm' : 'bg-white border-[#35A7FF]/30 hover:border-[#35A7FF] hover:shadow-sm' }}"
                    >
                        @if($isVideo && $thumbnailUrl)
                            <div class="relative aspect-video overflow-hidden bg-slate-200">
                                <div class="absolute inset-0 animate-pulse bg-slate-200"></div>

                                <img
                                    src="{{ $thumbnailUrl }}"
                                    alt="Thumbnail {{ $material->name }}"
                                    loading="lazy"
                                    class="relative h-full w-full object-cover"
                                    onload="this.previousElementSibling?.remove()"
                                    onerror="this.previousElementSibling?.remove(); this.remove();"
                                >

                                <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                                    <span class="grid h-11 w-11 place-items-center rounded-full bg-white/90 text-[#004777] shadow">
                                        ▶
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="flex aspect-video items-center justify-center bg-slate-100 relative overflow-hidden">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="Doc Thumbnail">
                                @endif
                                <div class="rounded-2xl border bg-white px-4 py-3 text-center relative z-10 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-500">
                                        {{ strtoupper($material->type) }}
                                    </div>

                                    <div class="mt-1 text-sm font-semibold text-slate-900">
                                        Document
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate font-semibold">
                                        {{ $material->name }}
                                    </div>

                                    <div class="mt-1 text-xs {{ $isActive ? 'text-white/70' : 'text-slate-500' }}">
                                        #{{ $material->sort_order }} · {{ ucfirst($material->status) }}
                                    </div>
                                </div>

                                <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-semibold
                                    {{ $isActive ? 'bg-white/10 text-white' : 'bg-[#35A7FF]/10 text-[#004777]/80' }}">
                                    {{ strtoupper($material->type) }}
                                </span>
                            </div>
                        </div>
                    </button>

                    @if($loop->last)
                        </div>
                    @endif
                @empty
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Materi sedang dipersiapkan</div>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Mentor belum mengunggah materi untuk topik ini.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                @if($activeMaterial)
                    @php
                        $activeCardData = $materialCards[(string) $activeMaterial->id] ?? [];
                        $finalPreviewUrl = $activeCardData['preview_url'] ?? $materialUrl;
                        $finalThumbnailUrl = $activeCardData['thumbnail_url'] ?? null;
                        $finalWatchUrl = $activeCardData['watch_url'] ?? null;
                        $finalSourceValue = $activeCardData['source_value'] ?? null;
                    @endphp

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold">
                                {{ $activeMaterial->name }}
                            </h2>

                            <p class="text-sm text-slate-500">
                                Type: {{ strtoupper($activeMaterial->type) }}
                            </p>
                        </div>

                        @if($canStudentInteract)
                            @if($activeMaterialProgress?->status === 'completed')
                                <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    MATERIAL COMPLETED
                                </span>
                            @else
                                <button
                                    type="button"
                                    x-on:click="openMaterialCompleteModal = true"
                                    class="px-4 py-2 rounded-xl bg-[#004777] text-white text-sm transition hover:bg-[#003560]"
                                >
                                    Mark Complete
                                </button>
                            @endif
                        @endif
                    </div>

                    <div wire:loading.class="opacity-60" wire:target="selectMaterial">
                        @if($materialUrl)
                            @if($activeMaterial->type === 'video')
                                <div class="max-w-3xl mx-auto space-y-4">
                                    <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-900 group shadow-sm border border-slate-200">
                                        @if($finalThumbnailUrl)
                                            <img
                                                src="{{ $finalThumbnailUrl }}"
                                                alt="Thumbnail {{ $activeMaterial->name }}"
                                                loading="lazy"
                                                class="h-full w-full object-cover opacity-80 transition duration-300 group-hover:scale-105 group-hover:opacity-100"
                                            >
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-slate-100">
                                                <span class="text-slate-400 text-sm">Thumbnail tidak tersedia</span>
                                            </div>
                                        @endif

                                        @if($finalWatchUrl)
                                            <a href="{{ $finalWatchUrl }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 flex items-center justify-center">
                                                <div class="grid h-16 w-16 place-items-center rounded-full bg-white/90 text-[#004777] shadow-lg transition-transform group-hover:scale-110">
                                                    <svg xmlns="http://www.w3.org" class="h-8 w-8 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </a>
                                        @endif
                                    </div>

                                    @if($finalWatchUrl)
                                        <div class="flex flex-wrap items-center justify-center gap-2">
                                            <a
                                                href="{{ $finalWatchUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="rounded-xl border px-5 py-2.5 text-sm font-semibold bg-[#004777] text-white hover:bg-[#003560] transition shadow-sm"
                                            >
                                                Tonton di YouTube
                                            </a>
                                        </div>
                                        <div class="text-center text-xs text-slate-500">
                                            Video akan dibuka di tab baru untuk pengalaman menonton terbaik.
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="overflow-hidden rounded-2xl border bg-slate-100">
                                        <iframe
                                            src="{{ $finalPreviewUrl }}"
                                            title="{{ $activeMaterial->name }}"
                                            class="h-[520px] w-full"
                                            loading="lazy"
                                        ></iframe>
                                    </div>

                                    <a
                                        href="{{ $materialUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white hover:bg-[#003560]"
                                    >
                                        Open / download material
                                    </a>
                                </div>

                            @endif
                        @else
                            <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                                <div class="font-semibold text-slate-900">
                                    Preview belum tersedia
                                </div>

                                <p class="text-sm text-slate-600 mt-1 leading-6">
                                    Material ini sudah terdaftar, tetapi URL/file preview belum valid atau belum tersedia.
                                </p>

                                @if($finalSourceValue)
                                    <div class="mt-3 rounded-xl bg-white border p-3 text-xs text-slate-500 break-all">
                                        {{ $finalSourceValue }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
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
                            Mentor belum menyiapkan materi untuk topik ini.
                        </p>
                    </div>
                @endif
            </div>

            @if($canStudentInteract && $activeMaterial)
                <div
                    x-show="openMaterialCompleteModal"
                    x-cloak
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    x-on:keydown.escape.window="openMaterialCompleteModal = false"
                >
                    <div
                        x-show="openMaterialCompleteModal"
                        x-transition
                        x-on:click.outside="openMaterialCompleteModal = false"
                        class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
                    >
                        <h3 class="text-lg font-semibold text-slate-950">
                            Tandai material selesai?
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Material <span class="font-semibold">{{ $activeMaterial->name }}</span> akan ditandai selesai. Progress topik akan diperbarui otomatis.
                        </p>

                        <div class="mt-5 flex justify-end gap-2">
                            <button
                                type="button"
                                x-on:click="openMaterialCompleteModal = false"
                                class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                wire:click="confirmMaterialCompletion"
                                wire:loading.attr="disabled"
                                wire:target="confirmMaterialCompletion"
                                class="rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white hover:bg-[#003560] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="confirmMaterialCompletion">
                                    Confirm
                                </span>
                                <span wire:loading wire:target="confirmMaterialCompletion">
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
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
                    $countdownText = $this->sessionCountdownLabel($session);
                    $startIso = $session->start_at?->toIso8601String();
                    $endIso = $session->end_at?->toIso8601String();

                    $attendanceBadgeClass = match ($attendance?->status) {
                        'present' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'late' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'absent' => 'bg-rose-100 text-rose-700 border-rose-200',
                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                    };
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
                            {{ $topic->course?->title }} · {{ $topic->name }}
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
                            <span class="text-xs px-2 py-1 rounded-full border {{ $attendanceBadgeClass }}">
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
                            <span class="px-4 py-2 rounded-xl border border-[#35A7FF]/30 bg-[#35A7FF]/10 text-xs text-[#004777]/80">
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

                                <button
                                    type="button"
                                    x-on:click="closeModal()"
                                    class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                                >
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
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border"
                                                :class="badgeClass"
                                                x-text="stateLabel"
                                            ></span>
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
                                        @disabled($phase !== 'live')
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
                        return 'bg-[#004777] text-white border-[#004777] hover:bg-[#003560]';
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

                    if (this.phase === 'ended') {
                        return 'Session completed';
                    }

                    let target = this.phase === 'upcoming' ? this.startAt : this.endAt;
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