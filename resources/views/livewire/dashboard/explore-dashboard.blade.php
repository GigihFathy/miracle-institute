@php
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-8">
    <section class="rounded-3xl overflow-hidden bg-slate-900 text-white">

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
                            <h1 class="text-4xl font-bold">
                                Expand Your Knowledge Without Limits
                            </h1>
                            <p class="text-slate-300">
                                Explore curated courses, structured topics, and professional learning paths.
                            </p>

                            <div class="flex gap-3">
                                <a href="{{ route('courses.index') }}"
                                class="px-5 py-3 bg-white text-black rounded-xl text-sm">
                                    Browse Courses
                                </a>
                                
                                @guest
                                    <a href="{{ route('login') }}"
                                    class="px-5 py-3 border border-white/30 rounded-xl text-sm">
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

                    <!-- LEFT CONTENT -->
                    <div class="p-8 sm:p-10 xl:p-12 space-y-6 flex flex-col justify-center">

                        <div class="space-y-2">
                            <div class="text-xs uppercase tracking-wide text-white/70">
                                Welcome back
                            </div>

                            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-bold leading-tight">
                                {{ auth()->user()->name ?? 'Learner' }},
                                continue your progress
                            </h1>

                            <p class="text-slate-300 max-w-xl">
                                Resume your learning path, track your progress, and unlock new achievements.
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
                            <a href="{{ route('courses.index') }}"
                            class="px-5 py-3 bg-white text-black rounded-xl text-sm font-medium">
                                Browse Courses
                            </a>

                            <a href="{{ route('learning.dashboard') }}"
                            class="px-5 py-3 border border-white/20 rounded-xl text-sm">
                                My Learning
                            </a>
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
                                Build Professional Skills with Structured Learning Paths
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </section>

     <section class="grid md:grid-cols-2 gap-4">

        <div class="bg-white border rounded-2xl p-5">
            <div class="text-sm text-slate-500">Total Courses</div>
            <div class="text-2xl font-bold mt-1">{{ $courses->total() }}</div>
        </div>

        <div class="bg-white border rounded-2xl p-5">
            <div class="text-sm text-slate-500">Programs</div>
            <div class="text-2xl font-bold mt-1">{{ $studyPrograms->count() }}</div>
        </div>
    </section>

    @if(!$isGuest && !$isMentor && count($continueCourses))
        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-semibold">Continue Learning</h2>
                <p class="text-sm text-slate-500">Resume your last activity.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                @foreach($continueCourses as $item)
                    <a href="{{ route('courses.show', $item->course->slug) }}"
                    class="bg-white border rounded-2xl p-5 hover:border-slate-400 transition">

                        <div class="font-semibold">{{ $item->course->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">
                            Continue where you left off
                        </div>

                        <div class="mt-3 h-2 bg-slate-100 rounded">
                            <div class="bg-slate-900 h-2 rounded w-1/2"></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="space-y-4">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-xl font-semibold">Categories</h2>
                <p class="text-sm text-slate-500">Explore based on study program.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="text-sm underline">Open course catalog</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($studyPrograms as $sp)
                <a href="{{ route('courses.index', ['studyProgram' => $sp->slug]) }}"
                   class="rounded-2xl bg-white border p-5 hover:border-slate-400 transition">
                    <div class="font-semibold">{{ $sp->title }}</div>
                    <div class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($sp->description, 70) }}</div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <h2 class="text-xl font-semibold">Highlights</h2>
            <p class="text-sm text-slate-500">Featured courses curated for discovery.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($featured as $course)
                <div class="rounded-2xl bg-white border overflow-hidden">
                    <div class="aspect-[16/9] bg-slate-100">
                        <img src="{{ asset('storage/' . $course->poster) }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ $course->studyProgram?->title }}</div>
                            <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
                            <p class="text-sm text-slate-600 mt-2">
                                {{ \Illuminate\Support\Str::limit($course->description, 110) }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span>{{ $course->topics_count }} topics</span>
                            <a href="{{ route('courses.show', $course->slug) }}" class="underline">Open</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-xl font-semibold">Explore all courses</h2>
                <p class="text-sm text-slate-500">Continue browsing below.</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white border p-4">
            <a href="{{ route('courses.index') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                Open Course Catalog
            </a>
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