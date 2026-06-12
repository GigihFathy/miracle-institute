@php
    use Illuminate\Support\Str;

    $calendarSessionItems = ($calendarSessions ?? collect())->map(function ($session) {
        return [
            'date' => $session->start_at?->format('Y-m-d'),
            'title' => $session->title,
            'topic' => $session->topic?->name ?? '-',
            'course' => $session->topic?->course?->title ?? '-',
            'time' => $session->start_at?->format('H:i') ?? '-',
            'datetime' => $session->start_at?->format('d M Y H:i') ?? '-',
            'status' => ucfirst($session->status),
            'url' => $session->topic?->course?->slug
                ? localized_route('courses.show', ['slug' => $session->topic->course->slug, 'tab' => 'topics', 'topic' => $session->topic_id])
                : null,
        ];
    });
    $calendarWeekdays = __('admin.dashboard.calendar.weekdays');
    $calendarMonths = __('admin.dashboard.calendar.months');

    $resolveCourseImage = function ($course) {
        $courseImage = $course?->poster ?? null;
        $courseImageSrc = null;

        if ($courseImage) {
            if (Str::startsWith($courseImage, ['http://', 'https://'])) {
                $courseImageSrc = $courseImage;
            } elseif (Str::startsWith($courseImage, 'images/')) {
                $courseImageSrc = asset($courseImage);
            } else {
                $courseImageSrc = asset('images/thumbnail/' . $courseImage);
            }
        } elseif (!empty($course?->image)) {
            $courseImageSrc = asset('storage/' . $course->image);
        }

        return $courseImageSrc;
    };
@endphp

<div class="min-h-screen bg-white pb-16 text-[#0f172a] sm:pb-24">
    <section class="px-4 pb-8 pt-8 sm:px-6 sm:pb-10 sm:pt-12 lg:px-8">
        <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.75fr)]">
            <div class="relative flex h-full overflow-hidden rounded-[2rem] bg-[#eef8ff] px-7 py-10 sm:px-10 sm:py-12 lg:px-14">
                <div class="relative flex h-full w-full flex-col">
                    <div class="max-w-2xl">
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#35A7FF]">
                            {{ __('general.my_learning.page_title') }}
                        </p>
                        <h1 class="mt-3 text-3xl font-bold leading-tight text-[#004777] sm:text-4xl">
                            {{ __('general.my_learning.overview_title') }}
                        </h1>
                        <p class="mt-4 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                            {{ __('general.my_learning.overview_description') }}
                        </p>
                    </div>

                    <div class="mt-auto grid grid-cols-2 gap-3 pt-8 sm:gap-4">
                        <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 backdrop-blur sm:px-5 sm:py-4">
                            <p class="text-3xl font-bold text-[#004777] sm:text-4xl">{{ $summary['courses_enrolled'] ?? 0 }}</p>
                            <p class="mt-1.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                {{ __('general.my_learning.metrics.courses_enrolled') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 backdrop-blur sm:px-5 sm:py-4">
                            <p class="text-3xl font-bold text-[#35A7FF] sm:text-4xl">{{ $summary['certificates'] ?? 0 }}</p>
                            <p class="mt-1.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                {{ __('general.my_learning.metrics.certificates') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] border border-[#d9ecfb] bg-white px-7 py-8 sm:px-8 sm:py-10">
                <div class="flex h-full flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="mt-3 text-2xl font-bold leading-tight text-[#004777] sm:text-3xl">
                                {{ __('general.my_learning.sessions.title') }}
                            </h2>
                        </div>
                    </div>

                    <div class="mt-6 flex-1 space-y-3">
                        @forelse($upcomingSessions->take(3) as $session)
                            <a
                                href="{{ localized_route('courses.show', ['slug' => $session->topic?->course?->slug, 'tab' => 'topics', 'topic' => $session->topic_id]) }}"
                                class="block rounded-2xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:border-[#35A7FF] hover:bg-white"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-base font-bold text-[#004777] lg:line-clamp-2 lg:whitespace-normal">
                                            {{ $session->topic?->name }}
                                        </p>
                                        <p class="mt-1 truncate text-sm text-slate-500 lg:line-clamp-1 lg:whitespace-normal">
                                            {{ $session->topic?->course?->title }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 rounded-xl bg-white px-3 py-2 text-right shadow-sm">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-[#35A7FF]">
                                            {{ $session->start_at->format('d M') }}
                                        </p>
                                        <p class="mt-1 text-sm font-bold text-[#004777]">
                                            {{ $session->start_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="flex h-full min-h-48 items-center justify-center rounded-2xl border border-dashed border-[#d7dcef] bg-[#f8fbff] px-6 text-center">
                                <div>
                                    <p class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.sessions.title') }}</p>
                                    <p class="mt-2 text-sm leading-6 text-[#5f6785]">{{ __('general.my_learning.sessions.empty') }}</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        <a
                            href="{{ localized_route('learning.dashboard', ['view' => 'sessions']) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18h-10.5A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2ZM3.5 8.5v6.75c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25V8.5h-13Zm1.25-3A1.25 1.25 0 0 0 3.5 6.75V7h13v-.25a1.25 1.25 0 0 0-1.25-1.25H4.75Z" clip-rule="evenodd" />
                            </svg>
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto w-full max-w-6xl space-y-8 px-4">
        @if($view === 'sessions')
        <section class="space-y-4 rounded-[1.75rem] border border-slate-200 bg-white p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.sessions.title') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih bulan dan tanggal untuk melihat semua sesi pembelajaran yang kamu ikuti.</p>
                </div>

                <a
                    href="{{ localized_route('learning.dashboard') }}"
                    class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                >
                    Kembali
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
                                    <span class="hidden text-[10px] font-semibold sm:inline" x-text="sessionCount(day) + ' sesi'"></span>
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
                        Tidak ada sesi pada tanggal ini.
                    </div>

                    <div x-show="!selectedDate" class="mt-4 text-sm text-slate-500">
                        Klik tanggal pada kalender untuk melihat sesi yang berlangsung pada hari itu.
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if($view === 'overview' || $view === 'courses')
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.courses.title') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Daftar kursus yang sedang kamu ikuti.</p>
                </div>

                <a
                    href="{{ localized_route('learning.dashboard', ['view' => $view === 'courses' ? 'overview' : 'courses']) }}"
                    class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                >
                    {{ $view === 'courses' ? 'Kembali' : 'Lihat Semua' }}
                </a>
            </div>

            @if($view === 'courses')
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative">
                        <input type="search"
                               wire:model.live.debounce.300ms="searchCourse"
                               placeholder="{{ __('general.my_learning.courses.search_placeholder') }}"
                               class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10 sm:w-64" />
                    </div>

                    <select wire:model.live="filterCourse" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10">
                        <option value="all">{{ __('general.my_learning.courses.filters.all') }}</option>
                        <option value="in_progress">{{ __('general.my_learning.courses.filters.in_progress') }}</option>
                        <option value="completed">{{ __('general.my_learning.courses.filters.completed') }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($filteredEnrollments as $row)
                        @php
                            $course = $row['enrollment']->course;
                            $courseImageSrc = $resolveCourseImage($course);
                        @endphp

                        <a href="{{ localized_route('courses.show', $course?->slug) }}" class="group flex h-full flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:border-[#35A7FF]">
                            <div class="relative overflow-hidden rounded-2xl">
                                @if($courseImageSrc)
                                    <img src="{{ $courseImageSrc }}" alt="{{ $course?->title }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>
                            </div>

                            <div class="flex flex-1 flex-col px-3 pb-3 pt-5">
                                <div class="flex-1 space-y-3">
                                    <div class="space-y-1.5 min-h-[76px]">
                                        <h3 class="line-clamp-2 text-xl font-bold leading-snug text-[#0f172a]">{{ $course?->title }}</h3>
                                        <p class="line-clamp-2 text-sm leading-6 text-slate-600">
                                            {{ $course?->description ?: __('general.my_learning.courses.no_description') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs text-[#004777]/70">
                                            <span>{{ __('general.my_learning.courses.progress_text', ['completed' => $row['completedTopics'], 'total' => $row['totalTopics']]) }}</span>
                                            <span class="font-semibold text-[#004777]">{{ $row['percent'] }}%</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-[#35A7FF]/15">
                                            <div class="h-2 rounded-full bg-[#004777]" style="width: {{ $row['percent'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                            @if($hasEnrollments)
                                <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.courses.empty.filtered_title') }}</h3>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">
                                    {{ __('general.my_learning.courses.empty.filtered_description') }}
                                </p>
                                <button type="button"
                                        wire:click="resetCourseFilters"
                                        class="mt-5 inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-medium text-[#004777] transition hover:bg-[#f4faff]">
                                    {{ __('general.my_learning.courses.reset_filters') }}
                                </button>
                            @else
                                <h3 class="text-base font-semibold text-[#004777]">
                                    {{ __('general.my_learning.courses.empty.no_courses_title') }}
                                </h3>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">
                                    {{ __('general.my_learning.courses.empty.no_courses_description') }}
                                </p>
                            @endif
                        </div>
                    @endforelse
                </div>
            @else
                <div class="flex gap-5 overflow-x-auto pb-2">
                    @forelse($coursePreview as $row)
                        @php
                            $course = $row['enrollment']->course;
                            $courseImageSrc = $resolveCourseImage($course);
                        @endphp

                        <a href="{{ localized_route('courses.show', $course?->slug) }}" class="group flex w-[290px] shrink-0 flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:border-[#35A7FF]">
                            <div class="relative overflow-hidden rounded-2xl">
                                @if($courseImageSrc)
                                    <img src="{{ $courseImageSrc }}" alt="{{ $course?->title }}" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-44 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20"></div>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col px-2 pb-2 pt-4">
                                <h3 class="line-clamp-2 text-lg font-bold leading-snug text-[#0f172a]">{{ $course?->title }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">
                                    {{ $course?->description ?: __('general.my_learning.courses.no_description') }}
                                </p>

                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center justify-between text-xs text-[#004777]/70">
                                        <span>{{ __('general.my_learning.courses.progress_text', ['completed' => $row['completedTopics'], 'total' => $row['totalTopics']]) }}</span>
                                        <span class="font-semibold text-[#004777]">{{ $row['percent'] }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-[#35A7FF]/15">
                                        <div class="h-2 rounded-full bg-[#004777]" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="w-full rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                            <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.courses.empty.no_courses_title') }}</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">{{ __('general.my_learning.courses.empty.no_courses_description') }}</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </section>
        @endif

        @if($view === 'overview' || $view === 'certificates')
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.certificates.title') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Semua sertifikat yang sudah berhasil kamu dapatkan.</p>
                </div>

                <a
                    href="{{ localized_route('learning.dashboard', ['view' => $view === 'certificates' ? 'overview' : 'certificates']) }}"
                    class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:bg-[#f4faff]"
                >
                    {{ $view === 'certificates' ? 'Kembali' : 'Lihat Semua' }}
                </a>
            </div>

            @if($view === 'certificates')
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative">
                        <input type="search"
                               wire:model.live.debounce.300ms="searchCertificate"
                               placeholder="Cari sertifikat..."
                               class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10 sm:w-64" />
                    </div>

                    <select wire:model.live="sortCertificate" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($filteredCertificates as $certificate)
                        @php
                            $course = $certificate->resolvedCourse();
                            $courseImageSrc = $resolveCourseImage($course);
                        @endphp

                        <article class="group flex h-full flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:-translate-y-1 hover:border-[#35A7FF]">
                            <div class="relative overflow-hidden rounded-2xl">
                                @if($courseImageSrc)
                                    <img src="{{ $courseImageSrc }}" alt="{{ $course?->title ?? __('general.my_learning.certificates.default_course_certificate') }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20"></div>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col px-3 pb-3 pt-5">
                                <div class="flex-1 space-y-3">
                                    <div class="space-y-1.5 min-h-19">
                                        <h3 class="line-clamp-2 text-xl font-bold leading-snug text-[#0f172a]">
                                            {{ $course?->title ?? __('general.my_learning.certificates.default_course_certificate') }}
                                        </h3>
                                        <p class="line-clamp-2 text-sm leading-6 text-slate-600">
                                            {{ $certificate->issued_at?->format('d M Y') ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs text-[#004777]/70">
                                            <span>{{ __('general.my_learning.certificates.number_label') }}</span>
                                            <span class="font-semibold text-[#004777]">{{ __('general.my_learning.certificates.issued_label') }}</span>
                                        </div>
                                        <div class="rounded-xl bg-[#f4faff] px-3 py-2 text-xs font-medium break-all text-[#004777]">
                                            {{ $certificate->certificate_number }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ localized_route('certificates.download', $certificate->id) }}"
                                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#003a5f]">
                                        {{ __('general.my_learning.certificates.download') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                            <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.certificates.empty_title') }}</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">{{ __('general.my_learning.certificates.empty_description') }}</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="flex gap-5 overflow-x-auto pb-2">
                    @forelse($certificatePreview as $certificate)
                        @php
                            $course = $certificate->resolvedCourse();
                            $courseImageSrc = $resolveCourseImage($course);
                        @endphp

                        <article class="group flex w-[290px] shrink-0 flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:-translate-y-1 hover:border-[#35A7FF]">
                            <div class="relative overflow-hidden rounded-2xl">
                                @if($courseImageSrc)
                                    <img src="{{ $courseImageSrc }}" alt="{{ $course?->title ?? __('general.my_learning.certificates.default_course_certificate') }}" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-44 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20"></div>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col px-2 pb-2 pt-4">
                                <h3 class="line-clamp-2 text-lg font-bold leading-snug text-[#0f172a]">
                                    {{ $course?->title ?? __('general.my_learning.certificates.default_course_certificate') }}
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $certificate->issued_at?->format('d M Y') ?? '-' }}
                                </p>

                                <div class="mt-4 rounded-xl bg-[#f4faff] px-3 py-2 text-xs font-medium break-all text-[#004777]">
                                    {{ $certificate->certificate_number }}
                                </div>

                                <a href="{{ localized_route('certificates.download', $certificate->id) }}"
                                   class="mt-4 inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#003a5f]">
                                    {{ __('general.my_learning.certificates.download') }}
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="w-full rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                            <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.certificates.empty_title') }}</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">{{ __('general.my_learning.certificates.empty_description') }}</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </section>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        function calendarComponent(sessions, weekdays, monthNames) {
            return {
                current: new Date(),
                sessions,
                weekdays,
                monthNames,
                days: [],
                blanks: [],
                monthYear: '',
                selectedDate: '',
                selectedSessions: [],
                selectedLabel: 'Belum ada tanggal dipilih',

                init() {
                    this.generate();
                },

                generate() {
                    const year = this.current.getFullYear();
                    const month = this.current.getMonth();
                    const firstDay = new Date(year, month, 1).getDay();
                    const totalDays = new Date(year, month + 1, 0).getDate();

                    this.blanks = Array.from({ length: firstDay }, (_, i) => i);
                    this.days = Array.from({ length: totalDays }, (_, i) => i + 1);
                    this.monthYear = `${this.monthNames[month] ?? ''} ${year}`.trim();

                    if (this.selectedDate) {
                        const selected = new Date(this.selectedDate);
                        if (selected.getFullYear() === year && selected.getMonth() === month) {
                            this.selectDay(selected.getDate());
                        } else {
                            this.selectedDate = '';
                            this.selectedSessions = [];
                            this.selectedLabel = 'Belum ada tanggal dipilih';
                        }
                    }
                },

                formatDate(day) {
                    const year = this.current.getFullYear();
                    const month = String(this.current.getMonth() + 1).padStart(2, '0');
                    return `${year}-${month}-${String(day).padStart(2, '0')}`;
                },

                hasSession(day) {
                    const dateStr = this.formatDate(day);
                    return this.sessions.some(session => session.date === dateStr);
                },

                sessionCount(day) {
                    const dateStr = this.formatDate(day);
                    return this.sessions.filter(session => session.date === dateStr).length;
                },

                selectDay(day) {
                    this.selectedDate = this.formatDate(day);
                    this.selectedSessions = this.sessions.filter(session => session.date === this.selectedDate);
                    this.selectedLabel = this.selectedDate;
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
            }
        }
    </script>
@endpush
