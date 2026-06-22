@php
    $isStudent = auth()->check() && session('active_role') === 'student';
@endphp

<div class="min-h-screen bg-white px-4 pb-16 pt-8 text-[#0f172a] sm:px-6 sm:pb-24 sm:pt-12 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-8">
    <section class="relative isolate overflow-hidden rounded-[2rem] bg-[#eef8ff] p-6 sm:p-10 lg:p-12">
        <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
            <div class="min-w-0">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#35A7FF]">
                    {{ $topic->course?->title }}
                </div>

                <div class="mt-3 space-y-3">
                    <h1 class="text-3xl font-bold leading-tight text-[#004777] sm:text-5xl">
                        {{ $topic->name }}
                    </h1>

                    <p class="max-w-3xl text-base leading-7 text-slate-600 sm:text-lg">
                        {{ $topic->description }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:w-[22rem]">
                <div class="rounded-2xl border border-white/80 bg-white/80 p-4 backdrop-blur">
                    <div class="text-3xl font-bold text-[#004777]">{{ $materials->count() }}</div>
                    <div class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ __('general.topic_player.stats.materials') }}
                    </div>
                </div>
                <div class="rounded-2xl border border-white/80 bg-white/80 p-4 backdrop-blur">
                    <div class="text-3xl font-bold text-[#35A7FF]">{{ $topic->videoSessions->count() }}</div>
                    <div class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ __('general.topic_player.stats.sessions') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-2">
        <div class="grid grid-cols-2 gap-2 text-sm font-semibold" role="tablist">
            <button
                type="button"
                role="tab"
                wire:click="setTab('materials')"
                aria-selected="{{ $activeTab === 'materials' ? 'true' : 'false' }}"
                class="rounded-xl px-4 py-3 transition {{ $activeTab === 'materials' ? 'bg-[#004777] text-white' : 'text-slate-500 hover:bg-[#eef8ff] hover:text-[#004777]' }}"
            >
                {{ __('general.topic_player.tabs.materials') }}
            </button>

            <button
                type="button"
                role="tab"
                wire:click="setTab('sessions')"
                aria-selected="{{ $activeTab === 'sessions' ? 'true' : 'false' }}"
                class="rounded-xl px-4 py-3 transition {{ $activeTab === 'sessions' ? 'bg-[#004777] text-white' : 'text-slate-500 hover:bg-[#eef8ff] hover:text-[#004777]' }}"
            >
                {{ __('general.topic_player.tabs.sessions') }}
            </button>
        </div>
    </section>

    @if($activeTab === 'materials')
        <section class="grid min-w-0 items-start gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <aside class="order-2 min-w-0 space-y-5 rounded-[1.5rem] border border-slate-200 bg-white p-4 sm:p-6 lg:sticky lg:top-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between sm:gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.topic_player.materials.title') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('general.topic_player.materials.subtitle') }}
                        </p>
                    </div>

                    <div wire:loading wire:target="selectMaterial" class="text-xs text-slate-500">
                        {{ __('general.topic_player.loading.select_material') }}
                    </div>
                </div>

                @forelse($materials as $material)
                    @if($loop->first)
                        <div class="flex min-w-0 flex-col gap-3 lg:max-h-[42rem] lg:overflow-y-auto lg:pr-1">
                    @endif

                    @php
                        $isActive = $activeMaterial?->id === $material->id;
                    @endphp

                    <button
                        type="button"
                        wire:key="material-card-{{ $material->id }}"
                        wire:click="selectMaterial(@js($material->id))"
                        wire:loading.attr="disabled"
                        wire:target="selectMaterial"
                        class="w-full min-w-0 overflow-hidden rounded-xl border text-left transition disabled:opacity-70
                        {{ $isActive ? 'border-[#004777] bg-[#004777] text-white' : 'border-slate-200 bg-white hover:border-[#35A7FF]' }}"
                    >
                        <div class="px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $isActive ? 'bg-white/15 text-white' : 'bg-[#eef8ff] text-[#004777]' }}">
                                        {{ $loop->iteration }}
                                    </span>

                                    <div class="min-w-0 break-words text-sm font-semibold">
                                        {{ $material->name }}
                                    </div>

                                </div>

                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide
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
                        <div class="font-semibold text-slate-900">{{ __('general.topic_player.materials.empty.title') }}</div>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            {{ __('general.topic_player.materials.empty.description') }}
                        </p>
                    </div>
                @endforelse
            </aside>

            <div
                class="order-1 min-w-0 space-y-5 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-4 sm:p-6"
                @if($activeMaterial)
                    wire:key="active-material-panel-{{ $activeMaterial->id }}"
                @endif
            >
                @if($activeMaterial)
                    @php
                        $activeCardData = $materialCards[(string) $activeMaterial->id] ?? [];
                        $finalPreviewUrl = $activeCardData['preview_url'] ?? $materialPreviewUrl;
                        $finalDownloadUrl = $activeCardData['download_url'] ?? null;
                        $finalThumbnailUrl = $activeCardData['thumbnail_url'] ?? null;
                        $finalWatchUrl = $activeCardData['watch_url'] ?? null;
                        $finalSourceValue = $activeCardData['source_value'] ?? null;
                        $youtubeId = $activeCardData['youtube_id'] ?? null;
                        $isTrackableVideo = $canStudentInteract
                            && $activeMaterial->type === 'video'
                            && filled($youtubeId);
                        $videoCompletionIsUnlocked = $videoCompletionUnlocked[(string) $activeMaterial->id] ?? false;
                    @endphp

                    <div
                        @if($isTrackableVideo && $canMarkComplete && $activeMaterialProgress?->status !== 'completed')
                            x-data="topicVideoProgressTracker({
                                materialId: @js((string) $activeMaterial->id),
                                youtubeId: @js($youtubeId),
                                requiredPercent: 70,
                                initiallyUnlocked: @js($videoCompletionIsUnlocked),
                                autoCompleteEnabled: @js($canMarkComplete),
                            })"
                            x-init="init()"
                        @endif
                        class="space-y-4"
                    >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h2 class="break-words text-xl font-bold text-[#004777] sm:text-2xl">
                                {{ $activeMaterial->name }}
                            </h2>

                            <p class="text-sm text-slate-500">
                                {{ __('general.topic_player.materials.type_label', ['type' => strtoupper($activeMaterial->type)]) }}
                            </p>
                        </div>

                        @if($canStudentInteract)
                            @if($activeMaterialProgress?->status === 'completed')
                                <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs text-emerald-700">
                                    {{ __('general.topic_player.materials.completed_badge') }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="confirmMaterialCompletion"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmMaterialCompletion"
                                    wire:confirm="{{ __('general.topic_player.materials.complete_modal.description', ['name' => $activeMaterial->name]) }}"
                                    @if($isTrackableVideo && $canMarkComplete)
                                        x-bind:disabled="!isUnlocked"
                                        x-bind:class="!isUnlocked ? 'cursor-not-allowed opacity-50' : ''"
                                    @endif
                                    class="w-full rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#003560] sm:w-auto"
                                >
                                    {{ __('general.topic_player.materials.mark_complete') }}
                                </button>
                            @endif
                        @endif
                    </div>

                    @if($isTrackableVideo && $canMarkComplete && $activeMaterialProgress?->status !== 'completed')
                        <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#eef8ff] p-4 text-sm text-[#004777]">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="font-semibold">Tonton minimal 70% video untuk menyelesaikan materi.</div>
                                    <div class="mt-1 text-xs text-slate-600" x-show="!isUnlocked">
                                        Sisa waktu tonton:
                                        <span class="font-semibold text-[#004777]" x-text="formattedRemaining"></span>
                                    </div>
                                    <div class="mt-1 text-xs text-emerald-700" x-show="isUnlocked">
                                        Progress menonton sudah memenuhi syarat. Materi akan selesai otomatis, atau bisa pakai tombol selesai jika diperlukan.
                                    </div>
                                </div>
                                <div class="shrink-0 rounded-full border border-[#35A7FF]/20 bg-white px-3 py-1 text-xs font-semibold text-[#004777]">
                                    <span x-text="progressLabel"></span>
                                </div>
                            </div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-[#35A7FF] transition-all duration-500" x-bind:style="`width: ${progressPercent}%`"></div>
                            </div>
                        </div>
                    @endif

                    <div wire:loading.class="opacity-60" wire:target="selectMaterial">
                        @if($materialUrl)
                            @if($activeMaterial->type === 'video')
                                <div class="space-y-4">
                                    <div class="min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-950 sm:rounded-2xl">
                                        @if($finalPreviewUrl)
                                            <iframe
                                                wire:key="material-video-frame-{{ $activeMaterial->id }}"
                                                src="{{ $finalPreviewUrl }}"
                                                title="{{ $activeMaterial->name }}"
                                                id="topic-video-player-{{ $activeMaterial->id }}"
                                                class="aspect-video w-full"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allowfullscreen
                                            ></iframe>
                                        @else
                                            <div class="flex aspect-video items-center justify-center bg-slate-100">
                                                <span class="text-sm text-slate-400">{{ __('general.topic_player.materials.thumbnail_not_available') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="min-w-0 overflow-hidden rounded-xl border bg-slate-100 sm:rounded-2xl">
                                        <iframe
                                            wire:key="material-document-frame-{{ $activeMaterial->id }}"
                                            src="{{ $finalPreviewUrl }}"
                                            title="{{ $activeMaterial->name }}"
                                            class="aspect-video w-full"
                                            loading="lazy"
                                        ></iframe>
                                    </div>

                                    @if($finalDownloadUrl)
                                        <a
                                            href="{{ $finalDownloadUrl }}"
                                            download
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#003560] sm:w-auto"
                                        >
                                            {{ __('general.topic_player.materials.open_download') }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                                <div class="font-semibold text-slate-900">
                                    {{ __('general.topic_player.materials.preview_not_available.title') }}
                                </div>

                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    {{ __('general.topic_player.materials.preview_not_available.description') }}
                                </p>

                                @if($finalSourceValue)
                                    <div class="mt-3 break-all rounded-xl border bg-white p-3 text-xs text-slate-500">
                                        {{ $finalSourceValue }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    </div>
                @elseif($hasMaterials)
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">{{ __('general.topic_player.materials.select_hint.title') }}</div>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ __('general.topic_player.materials.select_hint.description') }}
                        </p>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">{{ __('general.topic_player.materials.no_materials.title') }}</div>
                        <p class="mt-1 leading-6 text-sm text-slate-600">
                            {{ __('general.topic_player.materials.no_materials.description') }}
                        </p>
                    </div>
                @endif
            </div>

        </section>
    @endif

    @if($activeTab === 'sessions')
        <section class="space-y-4">
            <div class="space-y-4 rounded-[1.5rem] border border-slate-200 bg-white p-5 sm:p-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.topic_player.sessions.title') }}</h2>
                        <p class="text-sm text-slate-500">
                            {{ __('general.topic_player.sessions.subtitle') }}
                        </p>
                    </div>
                </div>
            </div>

            @forelse($topic->videoSessions as $session)
                @php
                    $attendance = $sessionAttendances->get($session->id);
                    $phase = $this->sessionPhase($session);
                    $phaseLabel = __('general.topic_player.sessions.states.' . $phase);
                    $buttonText = $this->sessionButtonText($session);
                    $countdownText = $this->sessionCountdownLabel($session);

                    $attendanceBadgeClass = match ($attendance?->status) {
                        'present' => 'border-emerald-200 bg-emerald-100 text-emerald-700',
                        'late' => 'border-amber-200 bg-amber-100 text-amber-700',
                        'online', 'absent' => 'border-sky-200 bg-sky-100 text-sky-700',
                        default => 'border-slate-200 bg-slate-100 text-slate-700',
                    };
                @endphp

                <div class="space-y-5 rounded-[1.5rem] border border-slate-200 bg-white p-5 transition hover:border-[#35A7FF] sm:p-6">
                    <div class="space-y-1">
                        <div class="text-lg font-bold text-[#004777]">{{ $session->title }}</div>
                        <div class="text-sm text-slate-500">
                            {{ $topic->course?->title }} · {{ $topic->name }}
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }} - {{ $session->end_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm text-slate-600">
                            {{ __('general.topic_player.sessions.status_label') }} <span class="font-medium">{{ $phaseLabel }}</span>
                        </div>

                        @if($attendance)
                            <span class="rounded-full border px-2 py-1 text-[11px] {{ $attendanceBadgeClass }}">
                                {{ strtoupper($attendance->status === 'absent' ? 'online' : $attendance->status) }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500">
                        {{ $countdownText }}
                    </div>

                    @if($attendance)
                        <div class="space-y-1 text-sm">
                            <div>{{ __('general.topic_player.sessions.check_in') }}: {{ $attendance->check_in_at?->format('d M Y, H:i') ?? '-' }}</div>
                            <div>{{ __('general.topic_player.sessions.check_out') }}: {{ $attendance->clock_out_at?->format('d M Y, H:i') ?? '-' }}</div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        @if($isStudent)
                            @if($attendance && ! $attendance->clock_out_at && $this->canClockOut($session, $attendance))
                                <button
                                    type="button"
                                    wire:click="clockOutSession('{{ $session->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="clockOutSession"
                                    class="rounded-xl border px-4 py-2 text-sm font-medium transition border-slate-300 bg-white text-slate-800 hover:bg-slate-50"
                                >
                                    {{ __('general.session_join_button.actions.clock_out') }}
                                </button>
                            @elseif($attendance && $attendance->clock_out_at)
                                <span class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-medium text-emerald-700">
                                    {{ __('general.session_join_button.attendance_completed') }}
                                </span>
                            @elseif($attendance && $phase === 'live')
                                <span class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-medium text-amber-700">
                                    Clock out tersedia mulai 15 menit sebelum pertemuan berakhir hingga 2 jam setelah pertemuan selesai
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="openSessionModal(@js($session->id))"
                                    wire:loading.attr="disabled"
                                    wire:target="openSessionModal"
                                    class="rounded-xl border px-4 py-2 text-sm font-medium transition {{ $this->sessionButtonClass($session) }}"
                                >
                                    {{ $buttonText }}
                                </button>
                            @endif
                        @else
                            <span class="rounded-xl border border-[#35A7FF]/30 bg-[#35A7FF]/10 px-4 py-2 text-xs text-[#004777]/80">
                                {{ __('general.topic_player.sessions.read_only') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                    <div class="font-semibold text-slate-900">{{ __('general.topic_player.sessions.empty.title') }}</div>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        {{ __('general.topic_player.sessions.empty.description') }}
                    </p>
                </div>
            @endforelse
        </section>
    @endif
    @if($showSessionModal && $selectedSession)
        @php
            $selectedPhase = $this->sessionPhase($selectedSession);
            $selectedPhaseLabel = __('general.topic_player.sessions.states.' . $selectedPhase);
            $selectedBadgeClass = $this->sessionBadgeClass($selectedSession);
            $selectedCountdown = $this->sessionCountdownLabel($selectedSession);
            $joinButtonClass = $selectedPhase === 'live'
                ? 'bg-[#004777] text-white border-[#004777] hover:bg-[#003560]'
                : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed';
        @endphp

        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
            wire:click="closeSessionModal"
        >
            <div class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white" wire:click.stop>
                <div class="flex items-start justify-between gap-4 border-b px-5 py-4 sm:px-6 sm:py-5">
                    <div>
                        <h3 class="text-xl font-bold text-[#004777]">{{ __('general.topic_player.sessions.join_modal.title') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('general.topic_player.sessions.join_modal.subtitle') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeSessionModal"
                        class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                    >
                        {{ __('general.topic_player.actions.close') }}
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5 sm:px-6 sm:py-6">
                    <div class="grid gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.title') }}</div>
                            <div class="mt-1 font-semibold text-slate-900">{{ $selectedSession->title }}</div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.status') }}</div>
                            <div class="mt-1">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $selectedBadgeClass }}">
                                    {{ $selectedPhaseLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.start') }}</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $selectedSession->start_at?->format('d M Y, H:i') ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.end') }}</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $selectedSession->end_at?->format('d M Y, H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">{{ __('general.topic_player.sessions.countdown') }}</div>
                        <div class="mt-1 text-base font-semibold text-slate-900">{{ $selectedCountdown }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        {{ __('general.topic_player.sessions.clock_in_deadline', ['time' => $selectedSession->clockInClosesAt()?->format('d M Y, H:i') ?? '-']) }}
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t pt-4">
                        <button
                            type="button"
                            wire:click="closeSessionModal"
                            class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"
                        >
                            {{ __('general.topic_player.actions.cancel') }}
                        </button>

                        <button
                            type="button"
                            wire:click="joinSession('{{ $selectedSession->id }}')"
                            wire:loading.attr="disabled"
                            wire:target="joinSession"
                            @disabled($selectedPhase !== 'live')
                            class="rounded-xl border px-4 py-2 text-sm font-medium transition {{ $joinButtonClass }}"
                        >
                            {{ __('general.topic_player.sessions.join_and_log') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

@once
    @push('scripts')
        <script>
            (() => {
                let youtubeApiReadyPromise;

                function loadYoutubeApi() {
                    if (window.YT && window.YT.Player) {
                        return Promise.resolve(window.YT);
                    }

                    if (youtubeApiReadyPromise) {
                        return youtubeApiReadyPromise;
                    }

                    youtubeApiReadyPromise = new Promise((resolve) => {
                        const previousHandler = window.onYouTubeIframeAPIReady;

                        window.onYouTubeIframeAPIReady = () => {
                            previousHandler?.();
                            resolve(window.YT);
                        };

                        const script = document.createElement('script');
                        script.src = 'https://www.youtube.com/iframe_api';
                        script.async = true;
                        document.head.appendChild(script);
                    });

                    return youtubeApiReadyPromise;
                }

                window.topicVideoProgressTracker = function topicVideoProgressTracker(config) {
                    return {
                        materialId: config.materialId,
                        youtubeId: config.youtubeId,
                        requiredPercent: config.requiredPercent ?? 70,
                        autoCompleteEnabled: !!config.autoCompleteEnabled,
                        isUnlocked: !!config.initiallyUnlocked,
                        autoCompleteTriggered: false,
                        player: null,
                        playerState: -1,
                        timer: null,
                        lastTickAt: null,
                        durationSeconds: 0,
                        watchedSeconds: 0,
                        progressPercent: config.initiallyUnlocked ? 100 : 0,
                        progressLabel: config.initiallyUnlocked ? '70% terpenuhi' : '0%',
                        formattedRemaining: '--:--',

                        async init() {
                            this.updateProgressUi();

                            if (!this.youtubeId || this.isUnlocked) {
                                return;
                            }

                            const yt = await loadYoutubeApi();
                            const elementId = `topic-video-player-${this.materialId}`;
                            const iframe = document.getElementById(elementId);

                            if (!iframe) {
                                return;
                            }

                            this.player = new yt.Player(elementId, {
                                events: {
                                    onReady: (event) => this.handleReady(event),
                                    onStateChange: (event) => this.handleStateChange(event),
                                },
                            });
                        },

                        handleReady(event) {
                            const duration = Number(event.target.getDuration?.() || 0);

                            if (duration > 0) {
                                this.durationSeconds = duration;
                            }

                            this.updateProgressUi();
                        },

                        handleStateChange(event) {
                            this.playerState = event.data;

                            if (event.data === window.YT.PlayerState.PLAYING) {
                                this.startTimer();
                                return;
                            }

                            this.stopTimer();
                        },

                        startTimer() {
                            if (this.timer || this.isUnlocked) {
                                return;
                            }

                            this.lastTickAt = Date.now();
                            this.timer = window.setInterval(() => this.tick(), 500);
                        },

                        stopTimer() {
                            if (this.timer) {
                                window.clearInterval(this.timer);
                                this.timer = null;
                            }

                            this.lastTickAt = null;
                        },

                        tick() {
                            if (this.isUnlocked || this.playerState !== window.YT.PlayerState.PLAYING) {
                                return;
                            }

                            const now = Date.now();

                            if (this.lastTickAt === null) {
                                this.lastTickAt = now;
                                return;
                            }

                            const elapsedSeconds = Math.max(0, (now - this.lastTickAt) / 1000);
                            this.lastTickAt = now;

                            this.watchedSeconds += elapsedSeconds;
                            this.updateProgressUi();

                            if (this.progressPercent >= 100) {
                                this.finishThreshold();
                            }
                        },

                        updateProgressUi() {
                            const requiredSeconds = this.getRequiredSeconds();
                            const consumed = this.isUnlocked
                                ? requiredSeconds
                                : Math.min(this.watchedSeconds, requiredSeconds);
                            const remaining = Math.max(0, requiredSeconds - consumed);
                            const percent = requiredSeconds > 0
                                ? Math.min(100, Math.round((consumed / requiredSeconds) * 100))
                                : 0;

                            this.progressPercent = percent;
                            this.progressLabel = this.isUnlocked
                                ? '70% terpenuhi'
                                : `${percent}% dari target`;
                            this.formattedRemaining = this.formatDuration(remaining);
                        },

                        getRequiredSeconds() {
                            if (this.durationSeconds <= 0 && this.player?.getDuration) {
                                this.durationSeconds = Number(this.player.getDuration() || 0);
                            }

                            return this.durationSeconds > 0
                                ? this.durationSeconds * (this.requiredPercent / 100)
                                : 0;
                        },

                        async finishThreshold() {
                            if (this.isUnlocked) {
                                return;
                            }

                            this.isUnlocked = true;
                            this.stopTimer();
                            this.updateProgressUi();

                            await this.$wire.unlockVideoCompletion(this.materialId);

                            if (this.autoCompleteEnabled && !this.autoCompleteTriggered) {
                                this.autoCompleteTriggered = true;
                                await this.$wire.confirmMaterialCompletion();
                            }
                        },

                        formatDuration(totalSeconds) {
                            const safeSeconds = Math.max(0, Math.ceil(totalSeconds));
                            const minutes = Math.floor(safeSeconds / 60);
                            const seconds = safeSeconds % 60;

                            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                        },
                    };
                };
            })();
        </script>
    @endpush
@endonce
