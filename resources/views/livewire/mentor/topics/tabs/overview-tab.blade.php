<section class="space-y-5">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)] sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-[var(--mentor-primary)]">
                    {{ __('mentor.topic_tabs.overview.title') }}
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-[color:color-mix(in_oklab,#004777_70%,white)]">
                    {{ $topic->description ?: __('mentor.topic_tabs.overview.no_description') }}
                </p>
            </div>

            {{-- <div class="flex flex-wrap gap-2">
                @if($canManageMaterials)
                    <span class="inline-flex items-center rounded-full bg-[var(--mentor-primary)] px-3 py-1 text-xs font-medium text-white">
                        {{ __('mentor.topic_tabs.overview.access.materials') }}
                    </span>
                @endif

                @if($canManageSessions)
                    <span class="inline-flex items-center rounded-full bg-[var(--mentor-secondary-solid)] px-3 py-1 text-xs font-medium text-[var(--mentor-primary)]">
                        {{ __('mentor.topic_tabs.overview.access.sessions') }}
                    </span>
                @endif

                @if($canManageStudents)
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-[var(--mentor-primary-soft)] px-3 py-1 text-xs font-medium text-[var(--mentor-primary)]">
                        {{ __('mentor.topic_tabs.overview.access.students') }}
                    </span>
                @endif
            </div> --}}
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">
                    {{ __('mentor.topic_tabs.overview.cards.category') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">
                    {{ strtoupper($topic->category ?? '-') }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">
                    {{ __('mentor.topic_tabs.overview.cards.visibility') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">
                    {{ strtoupper($topic->visibility) }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">
                    {{ __('mentor.topic_tabs.overview.cards.materials') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">
                    {{ $materialsCount }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">
                    {{ __('mentor.topic_tabs.overview.cards.session_status') }}
                </div>

                <div class="mt-2">
                    <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wide text-[var(--mentor-primary)]">
                        {{ str_replace('_', ' ', $sessionStatus) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
