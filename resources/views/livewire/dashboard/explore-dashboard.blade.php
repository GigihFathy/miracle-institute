@php
    $isMentor = session('active_role') === 'disciples';
    $studyProgramCount = $studyPrograms->count();
    $studyProgramCarousel = $studyProgramCount > 3;
@endphp

<div class="space-y-6 px-4 sm:px-5 lg:px-20 xl:px-28">
    <section class="overflow-hidden rounded-3xl bg-slate-900 text-white scale-[0.90] origin-top">

        @if($isGuest || $isMentor)
            <div x-data="slider()" x-init="init()" class="relative">
                <div class="relative h-[340px] sm:h-[380px] overflow-hidden">
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
                            <h1 class="text-3xl sm:text-4xl font-bold leading-tight">
                                Walking in Miracles, Growing as Disciples
                            </h1>

                            <p class="text-sm sm:text-base leading-relaxed text-slate-300">
                                Temukan perjalanan iman yang membawa Anda semakin dekat dengan Yesus melalui pemuridan, pembelajaran Alkitab, dan komunitas yang membangun kehidupan rohani.
                            </p>

                            <div class="flex flex-wrap gap-2.5 pt-1">
                                <a href="{{ route('courses.index') }}"
                                   class="rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-black transition hover:bg-slate-100">
                                    Explore Journey
                                </a>

                                @guest
                                    <a href="{{ route('login') }}"
                                       class="rounded-xl border border-white/30 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/10">
                                        Login
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
                                Welcome back to your spiritual journey
                            </div>

                            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-bold leading-tight">
                                {{ auth()->user()->name ?? 'Learner' }},
                                keep growing in faith
                            </h1>

                            <p class="max-w-xl text-sm sm:text-base leading-relaxed text-slate-300">
                                Lanjutkan perjalanan pemuridan Anda, pelajari kebenaran Firman Tuhan, dan alami pertumbuhan rohani yang nyata setiap hari.
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5 pt-1">
                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">Courses</div>
                                <div class="text-lg sm:text-xl font-bold">{{ $stats['courses'] ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">Completed</div>
                                <div class="text-lg sm:text-xl font-bold">{{ $stats['completed_topics'] ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white/10 p-3.5">
                                <div class="text-[11px] text-white/70">Progress</div>
                                <div class="text-lg sm:text-xl font-bold">{{ $stats['in_progress'] ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2.5 pt-1">
                            <a href="{{ route('courses.index') }}"
                               class="rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-black transition hover:bg-slate-100">
                                Explore Classes
                            </a>

                            <a href="{{ route('learning.dashboard') }}"
                               class="rounded-xl border border-white/20 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/10">
                                My Journey
                            </a>
                        </div>
                    </div>

                    <div class="relative h-[280px] sm:h-[340px] xl:h-full overflow-hidden">
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
                            <div class="text-xs text-white/70">Featured Program</div>
                            <div class="mt-1 text-base sm:text-lg font-semibold leading-snug">
                                Grow deeper in Christ through transformative biblical learning
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
                <h2 class="text-lg sm:text-xl font-semibold">Continue Your Journey</h2>
                <p class="text-sm text-slate-500">Continue where you left off in your discipleship journey.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                @foreach($continueCourses as $item)
                    @php
                        $progress = (int) ($item->progress_percentage ?? $item->progress ?? 50);
                        $progress = max(0, min(100, $progress));
                    @endphp

                    <a href="{{ route('courses.show', $item->course->slug) }}"
                       class="group overflow-hidden rounded-2xl transition hover:bg-slate-100">
                        <div class="p-2.5">
                            <div class="overflow-hidden rounded-lg thumb">
                                @if(!empty($item->course->image))
                                    <img src="{{ asset('storage/' . $item->course->image) }}"
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
                                <div class="text-[11px] uppercase tracking-wide text-slate-400">
                                    {{ $item->course->studyProgram?->title }}
                                </div>
                                <div class="mt-1 text-sm font-semibold leading-tight">
                                    {{ \Illuminate\Support\Str::limit($item->course->title, 70) }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">Continue where you left off</div>

                                <div class="mt-3">
                                    <div class="mb-1 flex items-center justify-between text-[11px] text-slate-500">
                                        <span>Progress</span>
                                        <span class="font-semibold text-slate-700">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-1.5 rounded-full bg-slate-900" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <span class="inline-flex rounded-lg bg-slate-900 px-3 py-1 text-xs font-medium text-white">
                                        Open
                                    </span>
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
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center">
                <div class="space-y-2.5 xl:basis-[30%]">
                    <h3 class="text-2xl sm:text-3xl font-bold leading-tight text-slate-900">
                        Grow in <em class="italic text-slate-500">faith and discover God’s</em> for your life
                    </h3>
                    <p class="text-sm leading-6 text-slate-500">
                        Explore discipleship programs, biblical teachings, and spiritual growth paths designed to strengthen your relationship with God.
                    </p>
                </div>

                <div class="min-w-0 flex-1 xl:basis-[70%]">
                    @if($studyProgramCarousel)
                        <div class="mb-3 flex items-center justify-end gap-2">
                            <button type="button" id="study-program-prev"
                                    class="nav-btn h-8 w-8 rounded-full border border-slate-200 bg-white text-slate-900 transition hover:bg-slate-100"
                                    aria-label="Scroll categories left">
                                ←
                            </button>

                            <button type="button" id="study-program-next"
                                    class="nav-btn h-8 w-8 rounded-full border border-slate-200 bg-white text-slate-900 transition hover:bg-slate-100"
                                    aria-label="Scroll categories right">
                                →
                            </button>
                        </div>

                        <div id="study-program-carousel" class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-3 scroll-smooth">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   data-study-program-card
                                   class="group flex h-40 w-[220px] shrink-0 snap-start flex-col justify-center rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-400 hover:shadow-md sm:w-[240px] lg:w-[260px]">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900/90 text-lg font-semibold text-white shadow-md">
                                            {{ strtoupper(mb_substr($sp->title, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-slate-900">{{ $sp->title }}</div>
                                            <div class="mt-1 truncate text-sm text-slate-500">
                                                {{ \Illuminate\Support\Str::limit($sp->description, 70) }}
                                            </div>
                                        </div>

                                        <div class="ml-1 text-lg text-slate-400 transition group-hover:text-slate-900">→</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="grid gap-4" style="grid-template-columns: repeat({{ $studyProgramCount }}, minmax(0, 1fr));">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   class="flex h-40 items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-slate-400 hover:shadow-sm">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900">{{ $sp->title }}</div>
                                        <div class="mt-1 text-sm text-slate-500">
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
            <h2 class="text-lg sm:text-xl font-semibold">Featured Teachings</h2>
            <p class="text-sm text-slate-500">Discover impactful teachings and discipleship classes prepared to strengthen your faith journey.</p>
        </div>

        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
            @foreach($featured as $course)
                <div class="cursor-pointer overflow-hidden rounded-2xl transition hover:bg-slate-100"
                     role="link" tabindex="0"
                     onclick="window.location='{{ route('courses.show', $course->slug) }}'"
                     onkeydown="if(event.key==='Enter'){ window.location='{{ route('courses.show', $course->slug) }}' }">
                    <div class="p-2.5">
                        <div class="overflow-hidden rounded-lg thumb">
                            @if(!empty($course->image))
                                <img src="{{ asset('storage/' . $course->image) }}"
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
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">
                                {{ $course->studyProgram?->title }}
                            </div>
                            <div class="card-title mt-1 text-sm font-semibold leading-tight">
                                {{ \Illuminate\Support\Str::limit($course->title, 70) }}
                            </div>
                            <div class="card-author mt-1 text-xs text-slate-500">
                                {{ $course->instructor?->name ?? $course->author ?? 'Instructor' }}
                            </div>

                            <div class="badges mt-2 flex flex-wrap gap-1.5">
                                @if(!empty($course->is_premium))
                                    <span class="badge badge-premium rounded px-2 py-0.5 text-[11px] bg-rose-50 text-rose-700">
                                        ⊙ Premium
                                    </span>
                                @endif
                                @if(!empty($course->is_bestseller))
                                    <span class="badge badge-bestseller rounded px-2 py-0.5 text-[11px] bg-blue-50 text-blue-700">
                                        Bestseller
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('courses.show', $course->slug) }}"
                                   class="inline-flex rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white">
                                    Open
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl bg-slate-900 px-6 py-6 text-white sm:px-8 lg:px-10">
        <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-2">
            <div>
                <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                    Experience God’s Presence Through Authentic Discipleship
                </h2>
                <p class="mt-2 max-w-xl text-xs sm:text-sm leading-6 text-slate-300">
                    Miracle Institute hadir untuk membantu setiap orang bertumbuh dalam iman, mengenal Yesus lebih dalam, dan hidup dalam kuasa serta kasih Tuhan setiap hari.
                </p>

                <div class="mt-4 grid grid-cols-2 gap-2.5">
                    <div class="flex items-start gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-800/60">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="#a78bfa" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Learn</div>
                            <div class="text-slate-300">Biblical truths and spiritual principles</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-800/60">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 2l1.5 3.5L13 6l-2.5 2.5.5 3.5L8 10.5 5 12l.5-3.5L3 6l3.5-.5z" stroke="#fbbf24" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Disciple</div>
                            <div class="text-slate-300">Grow deeper in biblical truth</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-800/60">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="9" rx="2" stroke="#60a5fa" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Community</div>
                            <div class="text-slate-300">Walk together in faith</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-800/60">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="#34d399" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Impact</div>
                            <div class="text-slate-300">Become a light for others</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('courses.index') }}"
                       class="inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900">
                        Start Your Journey
                    </a>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="flex items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-violet-600 p-2.5">
                    <svg class="h-40 w-40 sm:h-48 sm:w-48" viewBox="0 0 200 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="110" r="90" fill="rgba(255,255,255,0.08)"/>
                    </svg>
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