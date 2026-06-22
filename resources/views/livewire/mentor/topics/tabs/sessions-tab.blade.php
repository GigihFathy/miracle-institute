<section class="mentor-workspace-panel">
    <div>
        <h2 class="mentor-workspace-heading">{{ __('mentor.topic_tabs.sessions.title') }}</h2>
        <p class="mentor-workspace-subheading">{{ __('mentor.topic_tabs.sessions.subtitle') }}</p>
    </div>

    <div class="mt-5">
        @if($session)
            <div class="mentor-workspace-card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-[var(--mentor-primary)]">{{ $session->title }}</div>
                        <div class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">
                            {{ $session->start_at?->format('d M Y H:i') }} · {{ $session->end_at?->format('d M Y H:i') }}
                        </div>
                    </div>

                    <span class="rounded-full border border-slate-200 bg-white px-2 py-1 text-[11px] uppercase tracking-wide text-[var(--mentor-primary)]">
                        {{ $session->status }}
                    </span>
                </div>

                <div class="mt-3 space-y-1 text-sm">
                    <div class="truncate text-[var(--mentor-primary)]">
                        <span class="text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.sessions.zoom') }}:</span>
                        <a href="{{ $session->zoom_link }}" target="_blank" class="font-medium underline">{{ __('mentor.topic_tabs.sessions.open_link') }}</a>
                    </div>
                </div>
            </div>
        @else
            <div class="mentor-workspace-empty">
                {{ __('mentor.topic_tabs.sessions.empty') }}
            </div>
        @endif
    </div>

</section>
