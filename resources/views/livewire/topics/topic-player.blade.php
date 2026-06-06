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
        <section class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <aside class="space-y-5 rounded-[1.5rem] border border-slate-200 bg-white p-5 sm:p-6 lg:order-2 lg:sticky lg:top-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
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
                        <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory lg:max-h-[42rem] lg:flex-col lg:overflow-y-auto lg:overflow-x-hidden lg:pr-1">
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
                        class="w-[280px] shrink-0 snap-start overflow-hidden rounded-xl border text-left transition disabled:opacity-70 lg:w-full
                        {{ $isActive ? 'border-[#004777] bg-[#004777] text-white' : 'border-slate-200 bg-white hover:border-[#35A7FF]' }}"
                    >
                        <div class="px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $isActive ? 'bg-white/15 text-white' : 'bg-[#eef8ff] text-[#004777]' }}">
                                        {{ $loop->iteration }}
                                    </span>

                                    <div class="truncate text-sm font-semibold">
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
                class="min-w-0 space-y-5 rounded-[1.5rem] border border-slate-200 bg-white p-5 sm:p-6 lg:order-1"
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
                    @endphp

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-[#004777]">
                                {{ $activeMaterial->name }}
                            </h2>

                            <p class="text-sm text-slate-500">
                                {{ __('general.topic_player.materials.type_label', ['type' => strtoupper($activeMaterial->type)]) }}
                            </p>
                        </div>

                        @if($canStudentInteract)
                            @if($activeMaterialProgress?->status === 'completed')
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs text-emerald-700">
                                    {{ __('general.topic_player.materials.completed_badge') }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="confirmMaterialCompletion"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmMaterialCompletion"
                                    wire:confirm="{{ __('general.topic_player.materials.complete_modal.description', ['name' => $activeMaterial->name]) }}"
                                    class="rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#003560]"
                                >
                                    {{ __('general.topic_player.materials.mark_complete') }}
                                </button>
                            @endif
                        @endif
                    </div>

                    <div wire:loading.class="opacity-60" wire:target="selectMaterial">
                        @if($materialUrl)
                            @if($activeMaterial->type === 'video')
                                <div class="space-y-4">
                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                                        @if($finalPreviewUrl)
                                            <iframe
                                                wire:key="material-video-frame-{{ $activeMaterial->id }}"
                                                src="{{ $finalPreviewUrl }}"
                                                title="{{ $activeMaterial->name }}"
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
                                    <div class="overflow-hidden rounded-2xl border bg-slate-100">
                                        <iframe
                                            wire:key="material-document-frame-{{ $activeMaterial->id }}"
                                            src="{{ $finalPreviewUrl }}"
                                            title="{{ $activeMaterial->name }}"
                                            class="h-[520px] w-full"
                                            loading="lazy"
                                        ></iframe>
                                    </div>

                                    @if($finalDownloadUrl)
                                        <a
                                            href="{{ $finalDownloadUrl }}"
                                            download
                                            class="inline-flex rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#003560]"
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
                        'absent' => 'border-rose-200 bg-rose-100 text-rose-700',
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
                                {{ strtoupper($attendance->status) }}
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
                                    Clock out tersedia 15 menit sebelum sesi berakhir
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
                        {{ __('general.topic_player.sessions.clock_in_deadline', ['time' => $selectedSession->start_at?->copy()->addMinutes(45)?->format('d M Y, H:i') ?? '-']) }}
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
