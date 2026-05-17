@php
    $isMentor = session('active_role') === 'disciples';
    $studyProgramCount = $studyPrograms->count();
    $studyProgramCarousel = $studyProgramCount > 3;
@endphp

<div class="space-y-6 px-4 sm:px-5 lg:px-20 xl:px-28">
    <section class="overflow-hidden rounded-3xl bg-[#004777] text-white origin-top">
        @if($isGuest || $isMentor)
            <div x-data="slider()" x-init="init()" class="relative">
                <div class="relative h-[340px] overflow-hidden sm:h-[380px]">
                    <template x-for="(slide, index) in slides" :key="index">
                        <img
                            x-show="active === index"
                            x-transition
                            :src="slide"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                    </template>

                    <div class="absolute inset-0 bg-black/50"></div>

                    <div class="relative z-10 flex h-full items-center px-6 sm:px-8 lg:px-10">
                        <div class="max-w-xl space-y-3">
                            <h1 class="text-3xl font-bold leading-tight sm:text-4xl">
                                {{ __('general.explore_dashboard.hero.guest.title') }}
                            </h1>

                            <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                                {{ __('general.explore_dashboard.hero.guest.description') }}
                            </p>

                            <div class="flex flex-wrap gap-2.5 pt-1">
                                <a href="{{ localized_route('courses.index') }}"
                                   class="rounded-xl bg-[#35A7FF] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#35A7FF]/90">
                                    {{ __('general.explore_dashboard.hero.guest.explore_journey') }}
                                </a>

                                @guest
                                    <a href="{{ localized_route('login') }}"
                                       class="rounded-xl border border-white/30 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/10">
                                        {{ __('general.explore_dashboard.hero.guest.login') }}
                                    </a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div x-data="slider()" x-init="init()" class="relative">
                <div class="grid grid-cols-1 xl:grid-cols-2">
                    <div class="flex flex-col justify-center space-y-5 p-6 sm:p-8 xl:p-10">
                        <div class="space-y-2">
                            <div class="text-[11px] uppercase tracking-[0.2em] text-white/70">
                                {{ __('general.explore_dashboard.hero.member.welcome_back') }}
                            </div>

                            <h1 class="text-3xl font-bold leading-tight sm:text-4xl xl:text-5xl">
                                {{ auth()->user()->name ?? __('general.explore_dashboard.defaults.learner') }},
                                {{ __('general.explore_dashboard.hero.member.keep_growing') }}
                            </h1>

                            <p class="max-w-xl text-sm leading-relaxed text-slate-300 sm:text-base">
                                {{ __('general.explore_dashboard.hero.member.description') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5 pt-1">
                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">{{ __('general.explore_dashboard.hero.member.courses') }}</div>
                                <div class="text-lg font-bold sm:text-xl">{{ $stats['courses'] ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">{{ __('general.explore_dashboard.hero.member.completed') }}</div>
                                <div class="text-lg font-bold sm:text-xl">{{ $stats['completed_topics'] ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">{{ __('general.explore_dashboard.hero.member.progress') }}</div>
                                <div class="text-lg font-bold sm:text-xl">{{ $stats['in_progress'] ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2.5 pt-1">
                            <a href="{{ localized_route('courses.index') }}"
                               class="rounded-xl bg-[#35A7FF] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#35A7FF]/90">
                                {{ __('general.explore_dashboard.hero.member.explore_classes') }}
                            </a>

                            <a href="{{ localized_route('learning.dashboard') }}"
                               class="rounded-xl border border-white/20 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/10">
                                {{ __('general.explore_dashboard.hero.member.my_journey') }}
                            </a>
                        </div>
                    </div>

                    <div class="relative h-[280px] overflow-hidden sm:h-[340px] xl:h-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <img
                                x-show="active === index"
                                x-transition
                                :src="slide"
                                class="absolute inset-0 h-full w-full object-cover"
                            >
                        </template>

                        <div class="absolute inset-0 bg-black/30"></div>

                        <div class="absolute bottom-5 left-5 right-5 text-white">
                            <div class="text-xs text-white/70">{{ __('general.explore_dashboard.hero.member.featured_program') }}</div>
                            <div class="mt-1 text-base font-semibold leading-snug sm:text-lg">
                                {{ __('general.explore_dashboard.hero.member.featured_program_description') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </section>

    @if(!$isGuest && !$isMentor && count($continueCourses))
        <section class="space-y-3">
            <div>
                <h2 class="text-lg font-semibold sm:text-xl">{{ __('general.explore_dashboard.continue.title') }}</h2>
                <p class="text-sm text-[#004777]/70">{{ __('general.explore_dashboard.continue.description') }}</p>
            </div>

            <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-2 sm:-mx-5 sm:px-5 lg:mx-0 lg:px-0">
                @foreach($continueCourses as $item)
                    @php
                        $progress = (int) ($item->progress_percentage ?? 0);
                        $progress = max(0, min(100, $progress));

                        $courseImage = $item->course->poster ?? null;
                        $courseImageSrc = null;
                        if ($courseImage) {
                            if (\Illuminate\Support\Str::startsWith($courseImage, ['http://', 'https://'])) {
                                $courseImageSrc = $courseImage;
                            } else {
                                if (\Illuminate\Support\Str::startsWith($courseImage, 'images/')) {
                                    $courseImageSrc = asset($courseImage);
                                } else {
                                    $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                                }
                            }
                        }
                    @endphp

                    <a href="{{ localized_route('courses.show', $item->course->slug) }}"
                       class="group w-[220px] shrink-0 overflow-hidden rounded-2xl transition hover:bg-slate-100 sm:w-[240px] md:w-[250px]">
                        <div class="p-2.5">
                            <div class="overflow-hidden rounded-lg thumb">
                                @if($courseImageSrc)
                                    <img src="{{ $courseImageSrc }}"
                                         alt="{{ $item->course->title }}"
                                         class="h-32 w-full object-cover sm:h-36">
                                @else
                                    <div class="flex h-32 w-full items-center justify-center bg-slate-200 sm:h-36">
                                        <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="280" height="158" fill="#e6e9ee"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="text-[11px] uppercase tracking-wide text-[#35A7FF]/70">
                                    {{ $item->course->studyProgram?->title }}
                                </div>
                                <div class="mt-1 text-sm font-semibold leading-tight">
                                    {{ \Illuminate\Support\Str::limit($item->course->title, 70) }}
                                </div>
                                <div class="mt-1 text-xs text-[#004777]/70">{{ __('general.explore_dashboard.continue.continue_where_left_off') }}</div>

                                <div class="mt-3">
                                    <div class="mb-1 flex items-center justify-between text-[11px] text-[#004777]/70">
                                        <span>{{ __('general.explore_dashboard.continue.progress') }}</span>
                                        <span class="font-semibold text-[#004777]">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-[#35A7FF]/20">
                                        <div class="h-1.5 rounded-full bg-[#004777]" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($studyProgramCount)
        <section class="space-y-4 rounded-3xl p-4 sm:p-5 lg:p-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start">
                <div class="space-y-2.5 xl:basis-[30%] xl:self-stretch flex flex-col justify-center items-center">
                    <h3 class="text-2xl font-bold leading-tight text-[#004777] sm:text-3xl">
                        {{ __('general.explore_dashboard.study_programs.title') }}
                    </h3>
                    <p class="text-sm leading-6 text-[#004777]/70">
                        {{ __('general.explore_dashboard.study_programs.description') }}
                    </p>
                </div>

                <div class="min-w-0 flex-1 xl:basis-[70%]">
                    @if($studyProgramCarousel)
                        <div class="mb-3 hidden items-center justify-end gap-2 xl:flex">
                            <button type="button" id="study-program-prev"
                                    class="nav-btn h-8 w-8 rounded-full border border-[#004777]/15 bg-white text-[#004777] transition hover:bg-[#35A7FF]/10"
                                    aria-label="{{ __('general.explore_dashboard.study_programs.scroll_left') }}">
                                ←
                            </button>

                            <button type="button" id="study-program-next"
                                    class="nav-btn h-8 w-8 rounded-full border border-[#004777]/15 bg-white text-[#004777] transition hover:bg-[#35A7FF]/10"
                                    aria-label="{{ __('general.explore_dashboard.study_programs.scroll_right') }}">
                                →
                            </button>
                        </div>

                        <div id="study-program-carousel" class="grid grid-cols-1 gap-4 pb-3 md:grid-cols-2 xl:flex xl:snap-x xl:snap-mandatory xl:overflow-x-auto xl:scroll-smooth">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ localized_route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   data-study-program-card
                                   class="group flex h-40 w-full flex-col justify-center rounded-2xl border border-[#004777]/10 bg-[#35A7FF]/5 p-4 transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:shadow-md xl:w-[260px] xl:shrink-0 xl:snap-start sm:h-44">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#004777] text-base font-semibold text-white shadow-md sm:h-12 sm:w-12 sm:text-lg">
                                            {{ strtoupper(mb_substr($sp->title, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-[#004777] sm:text-base">{{ $sp->title }}</div>
                                            <div class="mt-1 truncate text-xs text-[#004777]/70 sm:text-sm">
                                                {{ \Illuminate\Support\Str::limit($sp->description, 70) }}
                                            </div>
                                        </div>

                                        <div class="ml-1 text-base text-[#35A7FF] transition group-hover:text-[#004777] sm:text-lg">→</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ localized_route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   class="flex min-h-40 items-center gap-4 rounded-2xl border border-[#004777]/10 bg-white p-5 transition hover:border-[#35A7FF] hover:shadow-sm">
                                    <div class="min-w-0">
                                        <div class="text-base font-semibold text-[#004777]">{{ $sp->title }}</div>
                                        <div class="mt-1 text-sm leading-6 text-[#004777]/70">
                                            {{ \Illuminate\Support\Str::limit($sp->description, 70) }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold sm:text-xl">{{ __('general.explore_dashboard.featured_teachings.title') }}</h2>
            <p class="text-sm text-[#004777]/70">{{ __('general.explore_dashboard.featured_teachings.description') }}</p>
        </div>

        <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-2 sm:-mx-5 sm:px-5 lg:mx-0 lg:px-0">
            @foreach($featured as $course)
                @php
                    $courseImage = $course->poster ?? null;
                    $courseImageSrc = null;
                    if ($courseImage) {
                        if (\Illuminate\Support\Str::startsWith($courseImage, ['http://', 'https://'])) {
                            $courseImageSrc = $courseImage;
                        } else {
                            if (\Illuminate\Support\Str::startsWith($courseImage, 'images/')) {
                                $courseImageSrc = asset($courseImage);
                            } else {
                                $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                            }
                        }
                    }
                @endphp

                 <div class="w-[220px] shrink-0 cursor-pointer overflow-hidden rounded-2xl transition hover:bg-slate-100 sm:w-[240px] md:w-[250px]"
                     role="link" tabindex="0"
                     onclick="window.location='{{ localized_route('courses.show', $course->slug) }}'"
                     onkeydown="if(event.key==='Enter'){ window.location='{{ localized_route('courses.show', $course->slug) }}' }">
                    <div class="p-2.5">
                        <div class="overflow-hidden rounded-lg thumb">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}"
                                     alt="{{ $course->title }}"
                                     class="h-32 w-full object-cover sm:h-36">
                            @else
                                <div class="flex h-32 w-full items-center justify-center bg-slate-200 sm:h-36">
                                    <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="280" height="158" fill="#e6e9ee"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="text-[11px] uppercase tracking-wide text-[#35A7FF]/70">
                                {{ $course->studyProgram?->title }}
                            </div>
                            <div class="card-title mt-1 text-sm font-semibold leading-tight">
                                {{ \Illuminate\Support\Str::limit($course->title, 70) }}
                            </div>
                            <div class="card-author mt-1 text-xs text-[#004777]/70">
                                {{ $course->instructor?->name ?? $course->author ?? __('general.explore_dashboard.defaults.instructor') }}
                            </div>

                            <div class="badges mt-2 flex flex-wrap gap-1.5">
                                @if(!empty($course->is_premium))
                                    <span class="badge badge-premium rounded px-2 py-0.5 text-[11px] bg-[#35A7FF]/10 text-[#004777]">
                                        ⊙ {{ __('general.explore_dashboard.featured_teachings.premium') }}
                                    </span>
                                @endif
                                @if(!empty($course->is_bestseller))
                                    <span class="badge badge-bestseller rounded px-2 py-0.5 text-[11px] bg-[#004777]/10 text-[#004777]">
                                        {{ __('general.explore_dashboard.featured_teachings.bestseller') }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a href="{{ localized_route('courses.show', $course->slug) }}"
                                   class="inline-flex rounded-lg bg-[#004777] px-3 py-1.5 text-xs font-medium text-white">
                                    {{ __('general.explore_dashboard.featured_teachings.open') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="!mt-8 overflow-hidden rounded-3xl bg-[#004777] px-4 py-6 text-white sm:px-6 lg:px-10">
        <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-2 lg:gap-8">
            <div>
                <h2 class="text-xl font-semibold leading-tight sm:text-2xl">
                    {{ __('general.explore_dashboard.cta.title') }}
                </h2>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/75 sm:text-base">
                    {{ __('general.explore_dashboard.cta.description') }}
                </p>

                <div class="mt-4 grid grid-cols-1 gap-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/10">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M13 4.5L6.5 11L3 7.5" stroke="#35A7FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <div class="font-semibold">{{ __('general.explore_dashboard.cta.learn.title') }}</div>
                            <div class="mt-0.5 text-white/75">{{ __('general.explore_dashboard.cta.learn.description') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/10">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M13 4.5L6.5 11L3 7.5" stroke="#35A7FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <div class="font-semibold">{{ __('general.explore_dashboard.cta.disciple.title') }}</div>
                            <div class="mt-0.5 text-white/75">{{ __('general.explore_dashboard.cta.disciple.description') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/10">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M13 4.5L6.5 11L3 7.5" stroke="#35A7FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <div class="font-semibold">{{ __('general.explore_dashboard.cta.community.title') }}</div>
                            <div class="mt-0.5 text-white/75">{{ __('general.explore_dashboard.cta.community.description') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/10">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M13 4.5L6.5 11L3 7.5" stroke="#35A7FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <div class="font-semibold">{{ __('general.explore_dashboard.cta.impact.title') }}</div>
                            <div class="mt-0.5 text-white/75">{{ __('general.explore_dashboard.cta.impact.description') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ localized_route('courses.index') }}"
                       class="inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-[#004777] hover:bg-white/90">
                        {{ __('general.explore_dashboard.cta.start_your_journey') }}
                    </a>
                </div>
            </div>
            
            <div class="flex justify-center lg:justify-end">
                <div class="w-full max-w-xl rounded-2xl p-1.5 sm:p-2.5">
                    <img src="{{ asset('images/decor/church_1.jpeg') }}"
                         alt="{{ __('general.explore_dashboard.defaults.church_illustration') }}"
                         class="h-48 w-full rounded-lg object-cover sm:h-64 lg:h-72">
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function slider() {
    return {
        active: 0,
        slides: [
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1600',
            'https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1600',
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=1600'
        ],

        init() {
            setInterval(() => {
                this.active = (this.active + 1) % this.slides.length;
            }, 10000);
        }
    }
}
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('study-program-carousel');

    if (!carousel) return;

    const prevButton = document.getElementById('study-program-prev');
    const nextButton = document.getElementById('study-program-next');

    const getScrollAmount = () => {
        const card = carousel.querySelector('[data-study-program-card]');
        if (!card) return 280;

        const cardWidth = card.getBoundingClientRect().width;
        const gap = 16;

        return cardWidth + gap;
    };

    prevButton?.addEventListener('click', () => {
        carousel.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });

    nextButton?.addEventListener('click', () => {
        carousel.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });
});
</script>
@endpush