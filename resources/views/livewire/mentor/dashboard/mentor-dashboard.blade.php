<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="{{ __('mentor.dashboard.page_title') }}"
        subtitle="{{ __('mentor.dashboard.page_subtitle') }}"
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 xl:grid-cols-3">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-[#004777]/60">
                {{ __('mentor.dashboard.stats.topics') }}
            </div>
            <div class="mt-2 text-3xl font-bold text-[#004777]">{{ $mentorTopicsCount }}</div>
            <div class="mt-1 text-sm text-[#004777]/70">
                {{ __('mentor.dashboard.stats.topics_hint') }}
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-[#004777]/60">
                {{ __('mentor.dashboard.stats.materials') }}
            </div>
            <div class="mt-2 text-3xl font-bold text-[#004777]">{{ $mentorMaterialsCount }}</div>
            <div class="mt-1 text-sm text-[#004777]/70">
                {{ __('mentor.dashboard.stats.materials_hint') }}
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-[#004777]/60">
                {{ __('mentor.dashboard.stats.students') }}
            </div>
            <div class="mt-2 text-3xl font-bold text-[#004777]">{{ $mentorStudentsCount }}</div>
            <div class="mt-1 text-sm text-[#004777]/70">
                {{ __('mentor.dashboard.stats.students_hint') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border bg-white p-5">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-[#004777]">
                        {{ __('mentor.dashboard.managed_courses.title') }}
                    </h2>
                    <p class="text-sm text-[#004777]/70">
                        {{ __('mentor.dashboard.managed_courses.subtitle') }}
                    </p>
                </div>
            </div>

            <div class="divide-y divide-[#004777]/10">
                @forelse($topicsByCourse as $courseTopics)
                    @php
                        $course = $courseTopics->first()->course;
                    @endphp

                    <div class="py-4" x-data="{ open: false }">
                        <button
                            type="button"
                            class="flex w-full items-start justify-between gap-4 text-left"
                            x-on:click="open = !open"
                            x-bind:aria-expanded="open.toString()"
                        >
                            <div>
                                <div class="text-sm font-semibold text-[#004777]">
                                    {{ $course?->title ?? __('mentor.dashboard.managed_courses.no_course') }}
                                </div>
                                <div class="mt-1 text-xs text-[#004777]/60">
                                    {{ $course?->studyProgram?->title ?? '-' }} · {{ __('mentor.dashboard.managed_courses.topic_count', ['count' => $courseTopics->count()]) }}
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="rounded-full border border-[#35A7FF]/30 bg-[#35A7FF]/10 px-2 py-1 text-xs text-[#004777]">
                                    {{ __('mentor.dashboard.managed_courses.active_badge') }}
                                </span>
                                <span class="text-xs font-medium text-[#004777]/60" x-text="open ? '{{ __('mentor.dashboard.managed_courses.hide') }}' : '{{ __('mentor.dashboard.managed_courses.show') }}'"></span>
                            </div>
                        </button>

                        <div class="mt-4 space-y-2" x-cloak x-show="open" x-transition>
                            @foreach($courseTopics->take(3) as $topic)
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-[#004777]">{{ $topic->name }}</div>
                                        <div class="text-xs text-[#004777]/60">
                                            {{ ucfirst($topic->status) }}
                                        </div>
                                    </div>

                                    <a href="{{ localized_route('mentor.topics.show', $topic->slug) }}"
                                       class="shrink-0 rounded-xl bg-[#004777] px-3 py-2 text-xs font-medium text-white hover:bg-[#003560]">
                                        {{ __('mentor.dashboard.managed_courses.manage') }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#35A7FF]/30 p-6 text-sm text-[#004777]/60">
                        {{ __('mentor.dashboard.managed_courses.empty') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border bg-white p-5">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-[#004777]">
                    {{ __('mentor.dashboard.recent_materials.title') }}
                </h2>
                <p class="text-sm text-[#004777]/70">
                    {{ __('mentor.dashboard.recent_materials.subtitle') }}
                </p>
            </div>

            <div class="divide-y divide-[#004777]/10">
                @forelse($latestMaterials as $material)
                    <div class="py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium text-[#004777]">{{ $material->name }}</div>
                                <div class="mt-1 truncate text-xs text-[#004777]/60">
                                    {{ $material->topic?->course?->title }} · {{ $material->topic?->name }} · {{ strtoupper($material->type) }}
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full border border-[#35A7FF]/30 bg-[#35A7FF]/10 px-2 py-1 text-[11px] uppercase tracking-wide text-[#004777]">
                                {{ $material->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-sm text-[#004777]/60">
                        {{ __('mentor.dashboard.recent_materials.empty') }}
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>