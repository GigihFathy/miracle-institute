@php
    $isMentor = session('active_role') === 'disciples';
    $studyProgramCount = $studyPrograms->count();
    $studyProgramCarousel = $studyProgramCount > 3;
@endphp

<div class="space-y-8 px-4 sm:px-6 lg:px-36">
    <section class="rounded-3xl overflow-hidden bg-primary-dark text-white">

        @if($isGuest || $isMentor)
            <div x-data="slider()" x-init="init()" class="relative">

                <div class="h-[420px] relative overflow-hidden">
                    <template x-for="(slide, index) in slides" :key="index">
                        <img x-show="active === index"
                            x-transition
                            :src="slide"
                            class="absolute inset-0 w-full h-full object-cover">
                    </template>

                    <div class="absolute inset-0 bg-black/50"></div>

                    <div class="relative z-10 h-full flex items-center px-10">
                        <div class="max-w-xl space-y-4">
                            <h1 class="text-4xl font-bold leading-tight">
                                Walking in Miracles, Growing as Disciples
                            </h1>

                            <p class="text-slate-300 text-base leading-relaxed">
                                Temukan perjalanan iman yang membawa Anda semakin dekat dengan Yesus melalui pemuridan, pembelajaran Alkitab, dan komunitas yang membangun kehidupan rohani.
                            </p>

                            <div class="flex gap-3">
                                <x-ui.button href="{{ route('courses.index') }}" variant="secondary" size="md">
                                    Explore Journey
                                </x-ui.button>
                                
                                @guest
                                    <x-ui.button href="{{ route('login') }}" variant="outline" size="md">
                                        Login
                                    </x-ui.button>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div x-data="slider()" x-init="init()" class="relative">

                <div class="grid grid-cols-1 xl:grid-cols-2">

                    <!-- LEFT CONTENT -->
                    <div class="p-8 sm:p-10 xl:p-12 space-y-6 flex flex-col justify-center">

                        <div class="space-y-2">
                            <div class="text-xs uppercase tracking-wide text-white/70">
                                Welcome back to your spiritual journey
                            </div>

                            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-bold leading-tight">
                                {{ auth()->user()->name ?? 'Learner' }},
                                keep growing in faith
                            </h1>

                            <p class="text-slate-300 max-w-xl">
                                Lanjutkan perjalanan pemuridan Anda, pelajari kebenaran Firman Tuhan, dan alami pertumbuhan rohani yang nyata setiap hari.
                            </p>
                        </div>

                        <!-- QUICK STATS -->
                        <div class="grid grid-cols-3 gap-3 pt-2">
                            <div class="bg-white/10 rounded-xl p-4">
                                <div class="text-xs text-white/70">Courses</div>
                                <div class="text-xl font-bold">{{ $stats['courses'] ?? 0 }}</div>
                            </div>

                            <div class="bg-white/10 rounded-xl p-4">
                                <div class="text-xs text-white/70">Completed</div>
                                <div class="text-xl font-bold">{{ $stats['completed_topics'] ?? 0 }}</div>
                            </div>

                            <div class="bg-white/10 rounded-xl p-4">
                                <div class="text-xs text-white/70">Progress</div>
                                <div class="text-xl font-bold">
                                    {{ $stats['in_progress'] ?? 0 }}
                                </div>
                            </div>
                        </div>

                        <!-- ACTION -->
                        <div class="flex flex-wrap gap-3 pt-2">
                            <x-ui.button href="{{ route('courses.index') }}" variant="secondary" size="md">
                                Explore Classes
                            </x-ui.button>

                            <x-ui.button href="{{ route('learning.dashboard') }}" variant="outline" size="md">
                                My Journey
                            </x-ui.button>
                        </div>
                    </div>

                    <!-- RIGHT SLIDER -->
                    <div class="relative h-[320px] sm:h-[380px] xl:h-full overflow-hidden">

                        <template x-for="(slide, index) in slides" :key="index">
                            <img x-show="active === index"
                                x-transition
                                :src="slide"
                                class="absolute inset-0 w-full h-full object-cover">
                        </template>

                        <!-- overlay -->
                        <div class="absolute inset-0 bg-black/30"></div>

                        <!-- caption -->
                        <div class="absolute bottom-6 left-6 right-6 text-white">
                            <div class="text-sm text-white/70">Featured Program</div>
                            <div class="text-lg font-semibold">
                                Grow deeper in Christ through transformative biblical learning
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </section>

    @if(!$isGuest && !$isMentor && count($continueCourses))
        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-semibold">Continue Your Journey</h2>
                <p class="text-sm text-slate-500">Continue where you left off in your discipleship journey.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($continueCourses as $item)
                    @php
                        $progress = (int) ($item->progress_percentage ?? $item->progress ?? 50);
                        $progress = max(0, min(100, $progress));
                    @endphp

                    <a href="{{ route('courses.show', $item->course->slug) }}"
                       class="rounded-2xl overflow-hidden group hover:bg-slate-100 transition">
                        <div class="p-3">
                            <div class="thumb rounded-md overflow-hidden">
                                @if(!empty($item->course->image))
                                    <img src="{{ asset('storage/' . $item->course->image) }}" alt="{{ $item->course->title }}" class="w-full h-36 object-cover">
                                @else
                                    <div class="w-full h-36 flex items-center justify-center bg-slate-200">
                                        <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="280" height="158" fill="#e6e9ee"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="text-xs uppercase tracking-wide text-slate-400">{{ $item->course->studyProgram?->title }}</div>
                                <div class="font-semibold text-sm mt-1 leading-tight">{{ \Illuminate\Support\Str::limit($item->course->title, 70) }}</div>
                                <div class="text-xs text-slate-500 mt-1">Continue where you left off</div>

                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                        <span>Progress</span>
                                        <span class="font-semibold text-slate-700">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-2 bg-primary rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <x-ui.badge variant="primary" size="sm">Open</x-ui.badge>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($studyProgramCount)
        <section class="space-y-5 rounded-3xl p-5 sm:p-6 lg:p-8">
            <div class="flex flex-col gap-10 xl:flex-row xl:items-center">
                <div class="space-y-3" style="flex-basis:30%">
                    <h3 class="text-2xl font-bold leading-tight text-slate-900">
                        Grow in <em class="italic text-slate-500">faith and discover God’s</em> for your life
                    </h3>
                    <p class="text-sm leading-6 text-slate-500">
                        Explore discipleship programs, biblical teachings, and spiritual growth paths designed to strengthen your relationship with God.
                    </p>
                </div>

                <div class="min-w-0 flex-1" style="flex-basis:70%">
                    @if($studyProgramCarousel)
                        <div class="mb-3 flex items-center justify-end gap-2">
                            <button type="button" id="study-program-prev"
                                    class="nav-btn h-9 w-9 rounded-full border border-primary bg-white text-primary transition hover:bg-primary/5"
                                    aria-label="Scroll categories left">
                                ←
                            </button>

                            <button type="button" id="study-program-next"
                                    class="nav-btn h-9 w-9 rounded-full border border-primary bg-white text-primary transition hover:bg-primary/5"
                                    aria-label="Scroll categories right">
                                →
                            </button>
                        </div>

                        <div id="study-program-carousel" class="flex gap-6 overflow-x-scroll pb-3 snap-x snap-mandatory scroll-smooth">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   data-study-program-card
                                   class="group shrink-0 w-[240px] sm:w-[260px] lg:w-[280px] snap-start rounded-2xl border border-slate-200 bg-slate-50 p-4 h-44 flex flex-col justify-center transition hover:-translate-y-0.5 hover:border-primary hover:shadow-md">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-14 rounded-full bg-primary text-white flex items-center justify-center text-xl font-semibold shadow-md">
                                            {{ strtoupper(mb_substr($sp->title, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 truncate">{{ $sp->title }}</div>
                                            <div class="mt-1 text-sm text-slate-500 truncate">{{ \Illuminate\Support\Str::limit($sp->description, 70) }}</div>
                                        </div>

                                        <div class="ml-2 text-xl text-slate-400 transition group-hover:text-slate-900">→</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="grid gap-6" style="grid-template-columns: repeat({{ $studyProgramCount }}, minmax(0, 1fr));">
                            @foreach($studyPrograms as $sp)
                                <a href="{{ route('courses.index', ['studyProgram' => $sp->slug]) }}"
                                   class="rounded-2xl border border-slate-200 bg-white p-12 h-44 flex items-center gap-4 transition hover:border-slate-400 hover:shadow-sm">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900">{{ $sp->title }}</div>
                                        <div class="mt-1 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($sp->description, 70) }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="space-y-4">
        <div>
            <h2 class="text-xl font-semibold">Featured Teachings</h2>
            <p class="text-sm text-slate-500">Discover impactful teachings and discipleship classes prepared to strengthen your faith journey.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($featured as $course)
                <div class="rounded-2xl overflow-hidden cursor-pointer group hover:bg-slate-100 transition"
                     role="link" tabindex="0"
                     onclick="window.location='{{ route('courses.show', $course->slug) }}'"
                     onkeydown="if(event.key==='Enter'){ window.location='{{ route('courses.show', $course->slug) }}' }">
                    <div class="p-3">
                        <div class="thumb rounded-md overflow-hidden">
                            @if(!empty($course->image))
                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-36 object-cover">
                            @else
                                <div class="w-full h-36 flex items-center justify-center bg-slate-200">
                                    <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="280" height="158" fill="#e6e9ee"/></svg>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ $course->studyProgram?->title }}</div>
                            <div class="card-title font-semibold text-sm mt-1 leading-tight">{{ \Illuminate\Support\Str::limit($course->title, 70) }}</div>
                            <div class="card-author text-xs text-slate-500 mt-1">{{ $course->instructor?->name ?? $course->author ?? 'Instructor' }}</div>

                            <div class="badges flex gap-2 mt-2">
                                @if(!empty($course->is_premium))
                                    <span class="badge badge-premium bg-rose-50 text-rose-700 px-2 py-0.5 rounded text-xs">⊙ Premium</span>
                                @endif
                                @if(!empty($course->is_bestseller))
                                    <span class="badge badge-bestseller bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs">Bestseller</span>
                                @endif
                            </div>

                            <div class="mt-4">
                                <x-ui.badge variant="primary" size="sm"><a href="{{ route('courses.show', $course->slug) }}">Open</a></x-ui.badge>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-3xl overflow-hidden bg-primary-dark text-white py-8 px-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
            <div class="lg:col-span-1 space-y-4">
                <h2 class="text-xl sm:text-2xl font-semibold">Grow in Faith Through Authentic Discipleship</h2>
                <p class="mt-1 text-xs text-slate-300 max-w-xl">Miracle Institute hadir untuk membantu setiap orang bertumbuh dalam iman, mengenal Yesus lebih dalam, dan hidup dalam kuasa serta kasih Tuhan setiap hari.</p>

                <div class="mt-3 grid grid-cols-2 gap-2">
                    <div class="flex items-start gap-2">
                        <div class="h-7 w-7 rounded-full bg-primary/20 flex items-center justify-center">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="#4BB3FD" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Learn</div>
                            <div class="text-slate-300">Biblical truths and spiritual principles</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="h-7 w-7 rounded-full bg-warning/20 flex items-center justify-center">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 2l1.5 3.5L13 6l-2.5 2.5.5 3.5L8 10.5 5 12l.5-3.5L3 6l3.5-.5z" stroke="#F59E0B" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Disciple</div>
                            <div class="text-slate-300">Grow deeper in biblical truth</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="h-7 w-7 rounded-full bg-info/20 flex items-center justify-center">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="9" rx="2" stroke="#3B82F6" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Community</div>
                            <div class="text-slate-300">Walk together in faith</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="h-7 w-7 rounded-full bg-success/20 flex items-center justify-center">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="#10B981" stroke-width="1"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-semibold">Impact</div>
                            <div class="text-slate-300">Become a light for others</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center gap-3">
                    <x-ui.button href="{{ route('courses.index') }}" variant="secondary" size="md">Start Your Journey</x-ui.button>
                </div>
            </div>

            <div class="gap-4">
                <div class="rounded-lg panel-abstract bg-gradient-to-br from-sky-400 to-violet-600 flex items-center justify-center p-2">
                    <svg class="w-48 h-48" viewBox="0 0 200 300" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="110" r="90" fill="rgba(255,255,255,0.08)"/></svg>
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

    if (!carousel) {
        return;
    }

    const prevButton = document.getElementById('study-program-prev');
    const nextButton = document.getElementById('study-program-next');

    const getScrollAmount = () => {
        const card = carousel.querySelector('[data-study-program-card]');

        if (!card) {
            return 320;
        }

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