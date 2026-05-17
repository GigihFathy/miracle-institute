@php
    $isStudent = auth()->check() && session('active_role') === 'student';
@endphp

<div class="space-y-6 pb-10 lg:px-36">
    <section class="space-y-5 rounded-3xl border bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 space-y-3">
                <div class="text-xs uppercase tracking-wide text-[#004777]/60">
                    {{ $topic->course?->title }}
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl font-bold text-[#004777] sm:text-3xl">
                        {{ $topic->name }}
                    </h1>

                    <p class="max-w-3xl leading-7 text-[#004777]/75">
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

        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">{{ __('general.topic_player.stats.materials') }}</div>
                <div class="mt-1 text-2xl font-bold text-[#004777]">
                    {{ $materials->count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">{{ __('general.topic_player.stats.attendance_records') }}</div>
                <div class="mt-1 text-2xl font-bold text-[#004777]">
                    {{ $attendanceStats['checked_in'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">{{ __('general.topic_player.stats.sessions') }}</div>
                <div class="mt-1 text-2xl font-bold text-[#004777]">
                    {{ $topic->videoSessions->count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#35A7FF]/8 p-4">
                <div class="text-xs text-[#004777]/70">{{ __('general.topic_player.stats.progress') }}</div>
                <div class="mt-1 text-2xl font-bold text-[#004777]">
                    @if($isMentor)
                        {{ __('general.topic_player.progress.review') }}
                    @else
                        {{ strtoupper($topicStatus ?? 'not_started') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="setTab('materials')"
                    class="rounded-xl border px-4 py-2 transition {{ $activeTab === 'materials' ? 'border-[#004777] bg-[#004777] text-white' : 'border-[#35A7FF]/30 bg-white text-[#004777] hover:bg-[#35A7FF]/10' }}"
                >
                    {{ __('general.topic_player.tabs.materials') }}
                </button>

                <button
                    type="button"
                    wire:click="setTab('sessions')"
                    class="rounded-xl border px-4 py-2 transition {{ $activeTab === 'sessions' ? 'border-[#004777] bg-[#004777] text-white' : 'border-[#35A7FF]/30 bg-white text-[#004777] hover:bg-[#35A7FF]/10' }}"
                >
                    {{ __('general.topic_player.tabs.sessions') }}
                </button>
            </div>

            @if($canStudentInteract && ! $topicCompleted && $activeMaterial && $isStudent)
                @php
                    $isLocked = ! $hasSessionEnded;
                @endphp

                <button
                    type="button"
                    wire:click="markViewed"
                    wire:loading.attr="disabled"
                    wire:target="markViewed"
                    @disabled($isLocked)
                    class="group relative flex items-center gap-2 rounded-xl border px-6 py-2 font-semibold transition-all duration-300
                    {{ $isLocked
                        ? 'cursor-not-allowed border-slate-200 bg-slate-50 text-slate-400 opacity-80'
                        : 'border-emerald-600 bg-emerald-600 text-white hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-200/50 active:translate-y-0'
                    }}"
                >
                    @if($isLocked)
                        <svg xmlns="http://www.w3.org" class="h-4 w-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('general.topic_player.actions.attend_to_complete') }}</span>
                    @else
                        <svg xmlns="http://www.w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ __('general.topic_player.actions.complete_unit') }}</span>
                    @endif

                    @if($isLocked)
                        <span class="absolute left-1/2 -top-10 -translate-x-1/2 scale-0 whitespace-nowrap rounded bg-gray-800 p-2 text-xs text-white transition-all group-hover:scale-100">
                            {{ __('general.topic_player.actions.locked_tooltip') }}
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
            <div class="space-y-4 rounded-2xl border bg-white p-5">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">{{ __('general.topic_player.materials.title') }}</h2>
                        <p class="text-sm text-slate-500">
                            {{ __('general.topic_player.materials.subtitle') }}
                        </p>
                    </div>

                    <div wire:loading wire:target="selectMaterial" class="text-xs text-slate-500">
                        {{ __('general.topic_player.loading.select_material') }}
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
                        class="w-[280px] shrink-0 snap-start overflow-hidden rounded-2xl border text-left transition disabled:opacity-70
                        {{ $isActive ? 'border-[#004777] bg-[#004777] text-white shadow-sm' : 'border-[#35A7FF]/30 bg-white hover:border-[#35A7FF] hover:shadow-sm' }}"
                    >
                        @if($isVideo && $thumbnailUrl)
                            <div class="relative aspect-video overflow-hidden bg-slate-200">
                                <div class="absolute inset-0 animate-pulse bg-slate-200"></div>

                                <img
                                    src="{{ $thumbnailUrl }}"
                                    alt="{{ __('general.topic_player.materials.thumbnail_alt', ['name' => $material->name]) }}"
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
                            <div class="relative flex aspect-video items-center justify-center overflow-hidden bg-slate-100">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" class="absolute inset-0 h-full w-full object-cover opacity-30" alt="{{ __('general.topic_player.materials.doc_thumbnail_alt') }}">
                                @endif
                                <div class="relative z-10 rounded-2xl border bg-white px-4 py-3 text-center shadow-sm">
                                    <div class="text-xs font-semibold text-slate-500">
                                        {{ strtoupper($material->type) }}
                                    </div>

                                    <div class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ __('general.topic_player.materials.document_label') }}
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
                        <div class="font-semibold text-slate-900">{{ __('general.topic_player.materials.empty.title') }}</div>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            {{ __('general.topic_player.materials.empty.description') }}
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="space-y-4 rounded-2xl border bg-white p-5">
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
                                    x-on:click="openMaterialCompleteModal = true"
                                    class="rounded-xl bg-[#004777] px-4 py-2 text-sm text-white transition hover:bg-[#003560]"
                                >
                                    {{ __('general.topic_player.materials.mark_complete') }}
                                </button>
                            @endif
                        @endif
                    </div>

                    <div wire:loading.class="opacity-60" wire:target="selectMaterial">
                        @if($materialUrl)
                            @if($activeMaterial->type === 'video')
                                <div class="mx-auto max-w-3xl space-y-4">
                                    <div class="group relative aspect-video overflow-hidden rounded-2xl border border-slate-200 bg-slate-900 shadow-sm">
                                        @if($finalThumbnailUrl)
                                            <img
                                                src="{{ $finalThumbnailUrl }}"
                                                alt="{{ __('general.topic_player.materials.thumbnail_alt', ['name' => $activeMaterial->name]) }}"
                                                loading="lazy"
                                                class="h-full w-full object-cover opacity-80 transition duration-300 group-hover:scale-105 group-hover:opacity-100"
                                            >
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-slate-100">
                                                <span class="text-sm text-slate-400">{{ __('general.topic_player.materials.thumbnail_not_available') }}</span>
                                            </div>
                                        @endif

                                        @if($finalWatchUrl)
                                            <a href="{{ $finalWatchUrl }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 flex items-center justify-center">
                                                <div class="grid h-16 w-16 place-items-center rounded-full bg-white/90 text-[#004777] shadow-lg transition-transform group-hover:scale-110">
                                                    <svg xmlns="http://www.w3.org" class="ml-1 h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
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
                                                class="rounded-xl border bg-[#004777] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#003560]"
                                            >
                                                {{ __('general.topic_player.materials.watch_youtube') }}
                                            </a>
                                        </div>
                                        <div class="text-center text-xs text-slate-500">
                                            {{ __('general.topic_player.materials.youtube_hint') }}
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
                                        class="inline-flex rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#003560]"
                                    >
                                        {{ __('general.topic_player.materials.open_download') }}
                                    </a>
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
                            {{ __('general.topic_player.materials.complete_modal.title') }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {!! __('general.topic_player.materials.complete_modal.description', ['name' => e($activeMaterial->name)]) !!}
                        </p>

                        <div class="mt-5 flex justify-end gap-2">
                            <button
                                type="button"
                                x-on:click="openMaterialCompleteModal = false"
                                class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"
                            >
                                {{ __('general.topic_player.actions.cancel') }}
                            </button>

                            <button
                                type="button"
                                wire:click="confirmMaterialCompletion"
                                wire:loading.attr="disabled"
                                wire:target="confirmMaterialCompletion"
                                class="rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white hover:bg-[#003560] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="confirmMaterialCompletion">
                                    {{ __('general.topic_player.actions.confirm') }}
                                </span>
                                <span wire:loading wire:target="confirmMaterialCompletion">
                                    {{ __('general.topic_player.loading.processing') }}
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
            <div class="space-y-4 rounded-2xl border bg-white p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ __('general.topic_player.sessions.title') }}</h2>
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
                    $buttonText = $this->sessionButtonText($session);
                    $countdownText = $this->sessionCountdownLabel($session);
                    $startIso = $session->start_at?->toIso8601String();
                    $endIso = $session->end_at?->toIso8601String();

                    $attendanceBadgeClass = match ($attendance?->status) {
                        'present' => 'border-emerald-200 bg-emerald-100 text-emerald-700',
                        'late' => 'border-amber-200 bg-amber-100 text-amber-700',
                        'absent' => 'border-rose-200 bg-rose-100 text-rose-700',
                        default => 'border-slate-200 bg-slate-100 text-slate-700',
                    };

                    $sessionLabels = [
                        'scheduled' => __('general.topic_player.sessions.states.scheduled'),
                        'live' => __('general.topic_player.sessions.states.live'),
                        'completed' => __('general.topic_player.sessions.states.completed'),
                        'unavailable' => __('general.topic_player.sessions.states.unavailable'),
                        'not_started' => __('general.topic_player.sessions.actions.not_started'),
                        'join_session' => __('general.topic_player.sessions.actions.join_session'),
                        'completed_button' => __('general.topic_player.sessions.actions.completed'),
                        'unavailable_button' => __('general.topic_player.sessions.actions.unavailable'),
                        'countdown_invalid' => __('general.topic_player.sessions.countdown_invalid'),
                        'completed_label' => __('general.topic_player.sessions.completed_label'),
                        'starts_in' => __('general.topic_player.sessions.starts_in'),
                        'ends_in' => __('general.topic_player.sessions.ends_in'),
                    ];
                @endphp

                <div
                    x-data="sessionJoinCard({
                        startAt: @js($startIso),
                        endAt: @js($endIso),
                        initialPhase: @js($phase),
                        title: @js($session->title),
                        startLabel: @js($session->start_at?->format('d M Y, H:i') ?? '-'),
                        endLabel: @js($session->end_at?->format('H:i') ?? '-'),
                        labels: @js($sessionLabels),
                    })"
                    class="space-y-4 rounded-2xl border bg-white p-5 shadow-sm"
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
                            {{ __('general.topic_player.sessions.status_label') }} <span class="font-medium" x-text="stateLabel"></span>
                        </div>

                        @if($attendance)
                            <span class="rounded-full border px-2 py-1 text-[11px] {{ $attendanceBadgeClass }}">
                                {{ strtoupper($attendance->status) }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500" x-text="countdownLabel">
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
                            <button
                                type="button"
                                x-on:click="openModal()"
                                :disabled="!canJoin"
                                class="rounded-xl border px-4 py-2 text-sm font-medium transition"
                                :class="buttonClass"
                            >
                                <span x-text="buttonText">{{ $buttonText }}</span>
                            </button>
                        @else
                            <span class="rounded-xl border border-[#35A7FF]/30 bg-[#35A7FF]/10 px-4 py-2 text-xs text-[#004777]/80">
                                {{ __('general.topic_player.sessions.read_only') }}
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
                        <div class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
                            <div class="flex items-start justify-between gap-4 border-b px-5 py-4 sm:px-6 sm:py-5">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ __('general.topic_player.sessions.join_modal.title') }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ __('general.topic_player.sessions.join_modal.subtitle') }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    x-on:click="closeModal()"
                                    class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                                >
                                    {{ __('general.topic_player.actions.close') }}
                                </button>
                            </div>

                            <div class="space-y-4 px-5 py-5 sm:px-6 sm:py-6">
                                <div class="grid gap-3 text-sm sm:grid-cols-2">
                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.title') }}</div>
                                        <div class="mt-1 font-semibold text-slate-900">{{ $session->title }}</div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.status') }}</div>
                                        <div class="mt-1">
                                            <span
                                                class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                                                :class="badgeClass"
                                                x-text="stateLabel"
                                            ></span>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.start') }}</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-4">
                                        <div class="text-xs text-slate-500">{{ __('general.topic_player.sessions.meta.end') }}</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $session->end_at?->format('d M Y, H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <div class="text-xs uppercase tracking-wide text-slate-500">{{ __('general.topic_player.sessions.countdown') }}</div>
                                    <div class="mt-1 text-base font-semibold text-slate-900" x-text="countdownLabel"></div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                    {{ __('general.topic_player.sessions.clock_in_deadline', ['time' => $session->start_at?->copy()->addMinutes(45)?->format('d M Y, H:i') ?? '-']) }}
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t pt-4">
                                    <button
                                        type="button"
                                        x-on:click="closeModal()"
                                        class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"
                                    >
                                        {{ __('general.topic_player.actions.cancel') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="joinSession('{{ $session->id }}')"
                                        wire:loading.attr="disabled"
                                        @disabled($phase !== 'live')
                                        class="rounded-xl px-4 py-2 text-sm font-medium transition"
                                        :class="buttonClass"
                                    >
                                        {{ __('general.topic_player.sessions.join_and_log') }}
                                    </button>
                                </div>
                            </div>
                        </div>
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
</div>
@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sessionJoinCard', (cfg) => ({
                open: false,
                now: Date.now(),
                timer: null,

                labels: cfg.labels ?? {},

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
                    if (this.phase === 'upcoming') return this.labels.scheduled ?? 'Scheduled';
                    if (this.phase === 'live') return this.labels.live ?? 'Live';
                    if (this.phase === 'ended') return this.labels.completed ?? 'Completed';

                    return this.labels.unavailable ?? 'Unavailable';
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
                    if (this.phase === 'upcoming') return this.labels.not_started ?? 'Not Started';
                    if (this.phase === 'live') return this.labels.join_session ?? 'Join Session';
                    if (this.phase === 'ended') return this.labels.completed_button ?? 'Completed';

                    return this.labels.unavailable_button ?? 'Unavailable';
                },

                get countdownLabel() {
                    if (!this.startAt || !this.endAt) {
                        return this.labels.countdown_invalid ?? 'Session schedule is incomplete.';
                    }

                    if (this.phase === 'ended') {
                        return this.labels.completed_label ?? 'Session completed';
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
                        ? `${this.labels.starts_in ?? 'Starts in'} ${parts.join(' ')}`
                        : `${this.labels.ends_in ?? 'Ends in'} ${parts.join(' ')}`;
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