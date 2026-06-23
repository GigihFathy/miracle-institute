@php
    use Illuminate\Support\Str;

    $calendarSessionItems = ($calendarSessions ?? collect())->map(function ($session) {
        return [
            'date' => $session->start_at?->format('Y-m-d'),
            'title' => $session->title,
            'topic' => $session->topic?->name ?? '-',
            'course' => $session->topic?->course?->title ?? '-',
            'datetime' => $session->start_at?->format('d M Y H:i') ?? '-',
            'status' => ucfirst($session->status),
            'url' => $session->topic?->slug
                ? localized_route('mentor.topics.show', $session->topic->slug)
                : null,
        ];
    });
    $calendarWeekdays = __('admin.dashboard.calendar.weekdays');
    $calendarMonths = __('admin.dashboard.calendar.months');
@endphp

<div class="min-h-screen bg-white pb-16 text-[#0f172a] sm:pb-24">
    <main class="mx-auto max-w-6xl space-y-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
            <div class="relative overflow-hidden rounded-[2rem] bg-[#eef8ff] px-7 py-9 sm:px-10 sm:py-12">
                <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-[#35A7FF]/10 blur-3xl" aria-hidden="true"></div>

                <div class="relative max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#35A7FF]">
                        {{ __('mentor.dashboard.welcome.eyebrow') }}
                    </p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight text-[#004777] sm:text-4xl">
                        {{ __('mentor.dashboard.welcome.title', ['name' => auth()->user()->name]) }}
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-slate-600">
                        {{ __('mentor.dashboard.welcome.subtitle') }}
                    </p>
                </div>

                <div class="relative mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-5 backdrop-blur">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.topics') }}
                        </div>
                        <div class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorTopicsCount }}</div>
                        <p class="mt-2 text-sm text-slate-500">{{ __('mentor.dashboard.stats.topics_hint') }}</p>
                    </div>

                    <div class="rounded-2xl border border-white/80 bg-white/80 p-5 backdrop-blur">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.materials') }}
                        </div>
                        <div class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorMaterialsCount }}</div>
                        <p class="mt-2 text-sm text-slate-500">{{ __('mentor.dashboard.stats.materials_hint') }}</p>
                    </div>

                    <div class="rounded-2xl border border-white/80 bg-white/80 p-5 backdrop-blur">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('mentor.dashboard.stats.students') }}
                        </div>
                        <div class="mt-3 text-3xl font-bold text-[#004777]">{{ $mentorStudentsCount }}</div>
                        <p class="mt-2 text-sm text-slate-500">{{ __('mentor.dashboard.stats.students_hint') }}</p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] border border-[#d9ecfb] bg-white px-7 py-8 sm:px-8 sm:py-10">
                <div class="flex h-full flex-col">
                    <div>
                        <h2 class="text-2xl font-bold leading-tight text-[#004777] sm:text-3xl">
                            {{ __('mentor.dashboard.sessions.title') }}
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ __('mentor.dashboard.sessions.subtitle') }}
                        </p>
                    </div>

                    <div class="mt-6 flex-1 space-y-3">
                        @if($nearestUpcomingSession)
                            <a
                                href="{{ localized_route('mentor.topics.show', $nearestUpcomingSession->topic?->slug) }}"
                                class="block rounded-2xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:border-[#35A7FF] hover:bg-white"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-base font-bold text-[#004777] lg:line-clamp-2 lg:whitespace-normal">
                                            {{ $nearestUpcomingSession->topic?->name }}
                                        </p>
                                        <p class="mt-1 truncate text-sm text-slate-500 lg:line-clamp-1 lg:whitespace-normal">
                                            {{ $nearestUpcomingSession->topic?->course?->title }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 rounded-xl bg-white px-3 py-2 text-right shadow-sm">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-[#35A7FF]">
                                            {{ $nearestUpcomingSession->start_at?->format('d M') }}
                                        </p>
                                        <p class="mt-1 text-sm font-bold text-[#004777]">
                                            {{ $nearestUpcomingSession->start_at?->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @else
                            <div class="flex h-full min-h-48 items-center justify-center rounded-2xl border border-dashed border-[#d7dcef] bg-[#f8fbff] px-6 text-center">
                                <div>
                                    <p class="text-base font-semibold text-[#004777]">{{ __('mentor.dashboard.sessions.title') }}</p>
                                    <p class="mt-2 text-sm leading-6 text-[#5f6785]">{{ __('mentor.dashboard.sessions.empty') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        <a
                            href="{{ localized_route('mentor.dashboard', ['view' => 'sessions']) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18h-10.5A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2ZM3.5 8.5v6.75c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25V8.5h-13Zm1.25-3A1.25 1.25 0 0 0 3.5 6.75V7h13v-.25a1.25 1.25 0 0 0-1.25-1.25H4.75Z" clip-rule="evenodd" />
                            </svg>
                            {{ __('mentor.dashboard.sessions.view_more') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @if($view === 'sessions')
            <section class="space-y-4 rounded-[1.75rem] border border-slate-200 bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('mentor.dashboard.sessions.calendar_title') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('mentor.dashboard.sessions.calendar_subtitle') }}</p>
                    </div>

                    <a
                        href="{{ localized_route('mentor.dashboard') }}"
                        class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                    >
                        {{ __('mentor.dashboard.sessions.back') }}
                    </a>
                </div>

                <div
                    x-data="calendarComponent(@js($calendarSessionItems), @js($calendarWeekdays), @js($calendarMonths))"
                    x-init="init()"
                    class="space-y-4"
                >
                    <div class="flex items-center justify-between">
                        <button @click="prevMonth()" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-[#004777] transition hover:bg-slate-50" aria-label="Bulan sebelumnya">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M11.78 4.22a.75.75 0 0 1 0 1.06L7.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="font-semibold text-[#004777]" x-text="monthYear"></div>
                        <button @click="nextMonth()" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-[#004777] transition hover:bg-slate-50" aria-label="Bulan berikutnya">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 0 1-1.06-1.06L12.94 10 8.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-7 text-center text-xs font-medium text-slate-500">
                        <template x-for="day in weekdays" :key="day">
                            <div x-text="day"></div>
                        </template>
                    </div>

                    <div class="grid grid-cols-7 gap-2 text-sm">
                        <template x-for="blank in blanks" :key="'blank-' + blank">
                            <div></div>
                        </template>

                        <template x-for="day in days" :key="day">
                            <button
                                type="button"
                                @click="selectDay(day)"
                                :class="isSelected(day)
                                    ? 'border-[#35A7FF] bg-[#eef8ff] text-[#004777]'
                                    : (hasSession(day)
                                        ? 'border-sky-200 bg-sky-100 text-slate-700 hover:border-sky-300 hover:bg-sky-100'
                                        : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50')"
                                class="relative h-16 rounded-xl border p-2 text-left transition sm:h-20"
                            >
                                <div x-text="day" class="text-xs font-medium"></div>

                                <template x-if="sessionCount(day) > 0">
                                    <div class="absolute bottom-2 left-2 right-2 flex items-center justify-start sm:justify-between">
                                        <div class="h-2 w-2 rounded-full bg-blue-500 sm:hidden"></div>
                                        <span class="hidden text-[10px] font-semibold sm:inline" x-text="sessionCount(day) + ' pertemuan'"></span>
                                    </div>
                                </template>
                            </button>
                        </template>
                    </div>

                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <div class="text-sm font-semibold text-[#004777]" x-text="selectedLabel"></div>

                        <div class="mt-4 space-y-3" x-show="selectedSessions.length > 0">
                            <template x-for="session in selectedSessions" :key="session.datetime + session.title">
                                <a
                                    :href="session.url || '#'"
                                    :class="session.url ? 'hover:border-[#35A7FF] hover:bg-[#fdfefe]' : 'cursor-default'"
                                    class="block rounded-xl border border-slate-200 bg-white p-4 transition"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate font-medium text-slate-900 lg:whitespace-normal" x-text="session.title"></div>
                                            <div class="mt-1 flex flex-col gap-1 text-xs text-slate-500 sm:block">
                                                <span class="block max-w-[180px] truncate lg:inline lg:max-w-none lg:align-middle" x-text="session.topic"></span>
                                                <span class="hidden lg:inline"> • </span>
                                                <span class="block max-w-[180px] truncate lg:inline lg:max-w-none" x-text="session.course"></span>
                                            </div>
                                            <div class="mt-1 text-xs text-slate-400" x-text="session.datetime"></div>
                                        </div>
                                        <span class="shrink-0 self-start rounded bg-slate-100 px-2 py-1 text-[11px] text-slate-600" x-text="session.status"></span>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div x-show="selectedDate && selectedSessions.length === 0" class="mt-4 text-sm text-slate-500">
                            {{ __('mentor.dashboard.sessions.empty_selected') }}
                        </div>

                        <div x-show="!selectedDate" class="mt-4 text-sm text-slate-500">
                            {{ __('mentor.dashboard.sessions.click_day') }}
                        </div>
                    </div>
                </div>
            </section>
        @endif

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
                                } elseif ($thumbnailUrl = course_thumbnail_url($poster)) {
                                    $posterSrc = $thumbnailUrl;
                                } elseif ($course->poster) {
                                    $posterSrc = course_thumbnail_url($poster);
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

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calendarComponent', (sessions, weekdays, monthNames) => ({
            current: new Date(),
            sessions,
            weekdays,
            monthNames,
            days: [],
            blanks: [],
            monthYear: '',
            selectedDate: '',
            selectedSessions: [],
            selectedLabel: '',

            init() {
                this.generate();
            },

            generate() {
                const year = this.current.getFullYear();
                const month = this.current.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                this.blanks = Array.from({ length: firstDay }, (_, index) => index);
                this.days = Array.from({ length: daysInMonth }, (_, index) => index + 1);
                this.monthYear = `${this.monthNames[month]} ${year}`;

                if (this.selectedDate) {
                    const selected = new Date(this.selectedDate);
                    if (selected.getFullYear() !== year || selected.getMonth() !== month) {
                        this.selectedDate = '';
                        this.selectedSessions = [];
                        this.selectedLabel = '';
                    }
                }
            },

            formatDate(day) {
                const month = String(this.current.getMonth() + 1).padStart(2, '0');
                return `${this.current.getFullYear()}-${month}-${String(day).padStart(2, '0')}`;
            },

            hasSession(day) {
                return this.sessions.some(session => session.date === this.formatDate(day));
            },

            sessionCount(day) {
                return this.sessions.filter(session => session.date === this.formatDate(day)).length;
            },

            selectDay(day) {
                this.selectedDate = this.formatDate(day);
                this.selectedSessions = this.sessions.filter(session => session.date === this.selectedDate);
                this.selectedLabel = `${day} ${this.monthNames[this.current.getMonth()]} ${this.current.getFullYear()}`;
            },

            isSelected(day) {
                return this.selectedDate === this.formatDate(day);
            },

            prevMonth() {
                this.current = new Date(this.current.getFullYear(), this.current.getMonth() - 1, 1);
                this.generate();
            },

            nextMonth() {
                this.current = new Date(this.current.getFullYear(), this.current.getMonth() + 1, 1);
                this.generate();
            },
        }));
    });
</script>
@endpush
