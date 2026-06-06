@php
    use Illuminate\Support\Str;
@endphp

<div class="min-h-screen bg-white px-4 pb-16 pt-8 text-[#0f172a] sm:px-6 sm:pb-24 sm:pt-12 lg:px-8">
    <main class="mx-auto max-w-6xl space-y-8">
        <section class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-50 px-6 py-9 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-[#35A7FF]/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#35A7FF]">
                    {{ __('mentor.dashboard.page_title') }}
                </p>
                <h1 class="mt-3 text-3xl font-bold leading-tight text-[#004777] sm:text-5xl">
                    {{ __('mentor.dashboard.page_title') }}
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                    {{ __('mentor.dashboard.page_subtitle') }}
                </p>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.topics') }}
                        </p>
                        <p class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorTopicsCount }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-[#004777]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20v3H6.5A2.5 2.5 0 0 1 4 18.5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ __('mentor.dashboard.stats.topics_hint') }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.materials') }}
                        </p>
                        <p class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorMaterialsCount }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-[#004777]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 3h7l5 5v13H7V3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M14 3v5h5M10 13h6M10 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ __('mentor.dashboard.stats.materials_hint') }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.students') }}
                        </p>
                        <p class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorStudentsCount }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-[#004777]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 20v-1.5A3.5 3.5 0 0 0 12.5 15h-5A3.5 3.5 0 0 0 4 18.5V20M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 8a3 3 0 0 1 0 6M20 20v-1a3 3 0 0 0-2-2.83" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ __('mentor.dashboard.stats.students_hint') }}</p>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 sm:p-7">
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#004777]">
                            {{ __('mentor.dashboard.managed_courses.title') }}
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            {{ __('mentor.dashboard.managed_courses.subtitle') }}
                        </p>
                    </div>

                    <div class="relative lg:w-64">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="courseSearch"
                            placeholder="{{ __('mentor.dashboard.managed_courses.search_placeholder') }}"
                            class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-[#004777] outline-none transition placeholder:text-slate-400 focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10"
                        >
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($managedCourses as $course)
                        @php
                            $courseTopics = $course->topics;
                            $poster = $course->poster ?? $course->image ?? null;
                            $posterSrc = null;

                            if ($poster) {
                                if (Str::startsWith($poster, ['http://', 'https://'])) {
                                    $posterSrc = $poster;
                                } elseif (file_exists(public_path($poster))) {
                                    $posterSrc = asset($poster);
                                } elseif (file_exists(public_path('storage/' . $poster))) {
                                    $posterSrc = asset('storage/' . $poster);
                                } elseif ($course->poster) {
                                    $posterSrc = asset('images/thumbnail/' . $poster);
                                }
                            }
                        @endphp

                        <article class="overflow-hidden rounded-2xl border border-slate-200" x-data="{ open: false }">
                            <button
                                type="button"
                                class="flex w-full items-start justify-between gap-4 px-4 py-4 text-left transition hover:bg-slate-50 sm:px-5"
                                x-on:click="open = !open"
                                x-bind:aria-expanded="open.toString()"
                            >
                                <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                                    <div class="h-14 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 sm:h-16 sm:w-20">
                                        @if($posterSrc)
                                            <img src="{{ $posterSrc }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[#004777]">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="break-words font-bold text-[#004777]">
                                            {{ $course->title ?? __('mentor.dashboard.managed_courses.no_course') }}
                                        </h3>
                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            {{ $course->studyProgram?->title ?? '-' }} &middot;
                                            {{ trans_choice('mentor.dashboard.managed_courses.topic_count', $courseTopics->count(), ['count' => $courseTopics->count()]) }}
                                        </p>
                                    </div>
                                </div>

                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-[#004777]">
                                    <svg class="h-4 w-4 transition" x-bind:class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>

                            <div class="space-y-2 border-t border-slate-200 bg-slate-50 p-3 sm:p-4" x-cloak x-show="open" x-transition>
                                @foreach($courseTopics->take(3) as $topic)
                                    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="break-words text-sm font-semibold text-[#004777]">{{ $topic->name }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ ucfirst($topic->status) }}</div>
                                        </div>

                                        <a href="{{ localized_route('mentor.topics.show', $topic->slug) }}"
                                           class="inline-flex shrink-0 items-center justify-center rounded-lg bg-[#004777] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#00395f]">
                                            {{ __('mentor.dashboard.managed_courses.manage') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center text-sm text-slate-500">
                            {{ filled($courseSearch)
                                ? __('mentor.dashboard.managed_courses.not_found')
                                : __('mentor.dashboard.managed_courses.empty') }}
                        </div>
                    @endforelse
                </div>

                @if($managedCourses->hasPages())
                    <div class="mt-6 border-t border-slate-200 pt-5">
                        {{ $managedCourses->links() }}
                    </div>
                @endif
            </section>

            <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 sm:p-7">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-[#004777]">
                        {{ __('mentor.dashboard.recent_materials.title') }}
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        {{ __('mentor.dashboard.recent_materials.subtitle') }}
                    </p>
                </div>

                <div class="space-y-2">
                    @forelse($latestMaterials as $material)
                        <article class="rounded-xl border border-slate-200 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words text-sm font-semibold text-[#004777]">{{ $material->name }}</h3>
                                    <p class="mt-1 break-words text-xs leading-5 text-slate-500">
                                        {{ $material->topic?->course?->title }} &middot; {{ $material->topic?->name }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    {{ strtoupper($material->type) }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center text-sm text-slate-500">
                            {{ __('mentor.dashboard.recent_materials.empty') }}
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</div>
