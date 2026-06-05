@php
    $isMentor = session('active_role') === 'disciples';
    $studyProgramCount = $studyPrograms->count();
    $featuredCount = $featured->count();
    $continueCount = count($continueCourses);
@endphp

<div class="min-h-screen bg-white text-[#0f172a]">
    <section class="relative isolate overflow-hidden px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        {{-- Background decoration --}}
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-full bg-cover bg-center opacity-10"
            style="background-image: url('{{ asset('images/decor/background.png') }}');"
            aria-hidden="true"
        ></div>
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"></div>

        <div class="mx-auto max-w-5xl text-center">
            <div class="mx-auto mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#004777] to-[#35A7FF] text-white shadow-lg shadow-[#35A7FF]/20">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3.5l1.85 5.65h5.95l-4.8 3.5 1.85 5.65L12 14.8 7.15 18.3 9 12.65l-4.8-3.5h5.95L12 3.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="text-4xl font-bold leading-tight tracking-normal text-[#004777] sm:text-6xl lg:text-7xl">
                {{ __('general.explore_dashboard.hero.guest.title') }}
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl">
                {{ __('general.explore_dashboard.hero.guest.description') }}
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ localized_route('courses.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#004777]/15 transition hover:bg-[#00395f]">
                    {{ __('general.explore_dashboard.hero.guest.explore_journey') }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                @guest
                    <a href="{{ localized_route('login') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-[#004777]/20 bg-white px-5 py-3 text-sm font-semibold text-[#004777] transition hover:border-[#35A7FF] hover:text-[#00395f]">
                        {{ __('general.explore_dashboard.hero.guest.login') }}
                    </a>
                @endguest
            </div>

            <div class="mx-auto mt-12 grid max-w-3xl grid-cols-3 gap-4 sm:gap-8">
                <div>
                    <p class="text-3xl font-bold text-[#004777] sm:text-4xl">{{ $studyProgramCount }}+</p>
                    <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">{{ __('general.explore_dashboard.study_programs.title') }}</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-[#35A7FF] sm:text-4xl">{{ $featuredCount }}+</p>
                    <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">{{ __('general.explore_dashboard.featured_teachings.title') }}</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-[#004777] sm:text-4xl">{{ $continueCount }}+</p>
                    <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">{{ __('general.explore_dashboard.continue.title') }}</p>
                </div>
            </div>
        </div>
    </section>

    @if($studyProgramCount)
        <section id="features" class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mx-auto mb-12 max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-[#0f172a] sm:text-5xl">
                        {{ __('general.explore_dashboard.study_programs.title') }}
                    </h2>
                    <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">
                        {{ __('general.explore_dashboard.study_programs.description') }}
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($studyPrograms->take(4) as $index => $sp)
                        <a href="{{ localized_route('courses.index', ['studyProgram' => $sp->slug]) }}"
                           class="group rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:shadow-xl hover:shadow-[#004777]/5 sm:p-8">
                            <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl {{ $index % 2 === 0 ? 'bg-[#004777]' : 'bg-[#35A7FF]' }} text-xl font-bold text-white">
                                {{ strtoupper(mb_substr($sp->title, 0, 1)) }}
                            </div>
                            <h3 class="text-xl font-bold text-[#0f172a] sm:text-2xl">{{ $sp->title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                                {{ \Illuminate\Support\Str::limit($sp->description, 150) }}
                            </p>
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#004777]">
                                {{ __('general.explore_dashboard.hero.guest.explore_journey') }}
                                <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(!$isGuest && !$isMentor && $continueCount)
        <section class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-[#0f172a] sm:text-4xl">{{ __('general.explore_dashboard.continue.title') }}</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ __('general.explore_dashboard.continue.continue_where_left_off') }}</p>
                    </div>
                    <a href="{{ localized_route('courses.index') }}" class="text-sm font-semibold text-[#004777] hover:text-[#35A7FF]">
                        {{ __('general.explore_dashboard.hero.guest.explore_journey') }}
                    </a>
                </div>

                <div class="flex gap-4 overflow-x-auto pb-3">
                    @foreach($continueCourses as $item)
                        @php
                            $progress = (int) ($item->progress_percentage ?? 0);
                            $progress = max(0, min(100, $progress));

                            $courseImage = $item->course->poster ?? null;
                            $courseImageSrc = null;
                            if ($courseImage) {
                                if (\Illuminate\Support\Str::startsWith($courseImage, ['http://', 'https://'])) {
                                    $courseImageSrc = $courseImage;
                                } elseif (\Illuminate\Support\Str::startsWith($courseImage, 'images/')) {
                                    $courseImageSrc = asset($courseImage);
                                } else {
                                    $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                                }
                            }
                        @endphp

                        <a href="{{ localized_route('courses.show', $item->course->slug) }}"
                           class="w-[280px] shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:shadow-xl hover:shadow-[#004777]/5 sm:w-[320px]">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}" alt="{{ $item->course->title }}" class="h-40 w-full object-cover">
                            @else
                                <div class="flex h-40 w-full items-center justify-center bg-slate-100 text-sm text-slate-400">
                                    {{ __('general.explore_dashboard.featured_teachings.title') }}
                                </div>
                            @endif

                            <div class="p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-[#35A7FF]">{{ $item->course->studyProgram?->title }}</p>
                                <h3 class="mt-2 text-base font-bold leading-snug text-[#0f172a]">{{ \Illuminate\Support\Str::limit($item->course->title, 70) }}</h3>
                                <div class="mt-4">
                                    <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                                        <span>{{ __('general.explore_dashboard.continue.progress') }}</span>
                                        <span class="font-bold text-[#004777]">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-[#35A7FF]/15">
                                        <div class="h-2 rounded-full bg-[#004777]" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="community" class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="mx-auto grid max-w-6xl gap-10 md:grid-cols-2 md:items-center">
            <div>
                <h2 class="text-3xl font-bold leading-tight text-[#0f172a] sm:text-5xl">
                    {{ __('general.explore_dashboard.cta.title') }}
                </h2>
                <p class="mt-5 text-base leading-7 text-slate-600 sm:text-lg">
                    {{ __('general.explore_dashboard.cta.description') }}
                </p>

                <ul class="mt-8 space-y-4">
                    <li class="flex gap-3">
                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#35A7FF]/15 text-[#004777]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>{{ __('general.explore_dashboard.cta.learn.description') }}</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#35A7FF]/15 text-[#004777]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>{{ __('general.explore_dashboard.cta.disciple.description') }}</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#35A7FF]/15 text-[#004777]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>{{ __('general.explore_dashboard.cta.community.description') }}</span>
                    </li>
                </ul>

                <a href="{{ localized_route('courses.index') }}"
                   class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f]">
                    {{ __('general.explore_dashboard.cta.start_your_journey') }}
                </a>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 p-3">
                <img src="{{ asset('images/decor/church_1.jpeg') }}"
                     alt="{{ __('general.explore_dashboard.defaults.church_illustration') }}"
                     class="h-80 w-full rounded-xl object-cover sm:h-96">
            </div>
        </div>
    </section>

    @if($featuredCount)
        <section id="impact" class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mx-auto mb-12 max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-[#0f172a] sm:text-5xl">{{ __('general.explore_dashboard.featured_teachings.title') }}</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">{{ __('general.explore_dashboard.featured_teachings.description') }}</p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    @foreach($featured->take(3) as $course)
                        @php
                            $courseImage = $course->poster ?? null;
                            $courseImageSrc = null;
                            if ($courseImage) {
                                if (\Illuminate\Support\Str::startsWith($courseImage, ['http://', 'https://'])) {
                                    $courseImageSrc = $courseImage;
                                } elseif (\Illuminate\Support\Str::startsWith($courseImage, 'images/')) {
                                    $courseImageSrc = asset($courseImage);
                                } else {
                                    $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                                }
                            }
                        @endphp

                        <a href="{{ localized_route('courses.show', $course->slug) }}"
                           class="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:shadow-xl hover:shadow-[#004777]/5">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}" alt="{{ $course->title }}" class="h-44 w-full object-cover">
                            @else
                                <div class="flex h-44 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/10 text-[#004777]">
                                    {{ __('general.explore_dashboard.featured_teachings.title') }}
                                </div>
                            @endif

                            <div class="p-6">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#35A7FF]">{{ $course->studyProgram?->title }}</p>
                                <h3 class="mt-2 text-lg font-bold leading-snug text-[#0f172a]">{{ \Illuminate\Support\Str::limit($course->title, 80) }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $course->instructor?->name ?? $course->author ?? __('general.explore_dashboard.defaults.instructor') }}
                                </p>

                                <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#004777]">
                                    {{ __('general.explore_dashboard.featured_teachings.open') }}
                                    <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-3xl font-bold leading-tight text-[#0f172a] sm:text-5xl">
                {{ __('general.explore_dashboard.cta.title') }}
            </h2>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-xl">
                {{ __('general.explore_dashboard.cta.description') }}
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ localized_route('courses.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f]">
                    {{ __('general.explore_dashboard.cta.start_your_journey') }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                @guest
                    <a href="{{ localized_route('login') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-[#004777]/20 bg-white px-5 py-3 text-sm font-semibold text-[#004777] transition hover:border-[#35A7FF]">
                        {{ __('general.explore_dashboard.hero.guest.login') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>
</div>
