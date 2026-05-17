<section class="space-y-5">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">
                    {{ __('mentor.topic_tabs.overview.title') }}
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                    {{ $topic->description ?: __('mentor.topic_tabs.overview.no_description') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($canManageMaterials)
                    <span class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">
                        {{ __('mentor.topic_tabs.overview.access.materials') }}
                    </span>
                @endif

                @if($canManageSessions)
                    <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-xs font-medium text-white">
                        {{ __('mentor.topic_tabs.overview.access.sessions') }}
                    </span>
                @endif

                @if($canManageStudents)
                    <span class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-xs font-medium text-white">
                        {{ __('mentor.topic_tabs.overview.access.students') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ __('mentor.topic_tabs.overview.cards.category') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ strtoupper($topic->category ?? '-') }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ __('mentor.topic_tabs.overview.cards.visibility') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ strtoupper($topic->visibility) }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ __('mentor.topic_tabs.overview.cards.materials') }}
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ $materialsCount }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ __('mentor.topic_tabs.overview.cards.session_status') }}
                </div>

                <div class="mt-2">
                    <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wide text-slate-700">
                        {{ str_replace('_', ' ', $sessionStatus) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>