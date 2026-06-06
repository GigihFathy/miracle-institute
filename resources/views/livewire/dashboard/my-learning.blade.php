@php
    use Illuminate\Support\Str;
@endphp

<div class="min-h-screen bg-white pb-16 text-[#0f172a] sm:pb-24">
    <section class="px-4 pb-8 pt-8 sm:px-6 sm:pb-10 sm:pt-12 lg:px-8">
        <div class="relative mx-auto overflow-hidden rounded-[2rem] bg-[#eef8ff] px-7 py-10 sm:px-10 sm:py-12 lg:max-w-6xl lg:px-14">
            <div class="relative grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
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

                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:w-[27rem]">
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 backdrop-blur sm:p-5">
                        <p class="text-3xl font-bold text-[#004777] sm:text-4xl">{{ $summary['courses_enrolled'] ?? 0 }}</p>
                        <p class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('general.my_learning.metrics.courses_enrolled') }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 backdrop-blur sm:p-5">
                        <p class="text-3xl font-bold text-[#35A7FF] sm:text-4xl">{{ $summary['certificates'] ?? 0 }}</p>
                        <p class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ __('general.my_learning.metrics.certificates') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-4 pb-8 sm:px-6 sm:pb-10 lg:px-8">
        <div class="mx-auto max-w-6xl rounded-2xl border border-slate-200 bg-white p-2">
            <div class="grid grid-cols-3 gap-2 text-sm font-semibold" role="tablist" aria-label="{{ __('general.my_learning.tabs.aria_label') }}">
                <button type="button" role="tab" wire:click="$set('tab','courses')" aria-selected="{{ $tab==='courses' ? 'true' : 'false' }}" class="{{ $tab==='courses' ? 'bg-[#004777] text-white' : 'text-slate-500 hover:bg-[#eef8ff] hover:text-[#004777]' }} rounded-xl px-3 py-3 transition">
                    {{ __('general.my_learning.tabs.courses') }}
                </button>
                <button type="button" role="tab" wire:click="$set('tab','session')" aria-selected="{{ $tab==='session' ? 'true' : 'false' }}" class="{{ $tab==='session' ? 'bg-[#004777] text-white' : 'text-slate-500 hover:bg-[#eef8ff] hover:text-[#004777]' }} rounded-xl px-3 py-3 transition">
                    {{ __('general.my_learning.tabs.session') }}
                </button>
                <button type="button" role="tab" wire:click="$set('tab','certificate')" aria-selected="{{ $tab==='certificate' ? 'true' : 'false' }}" class="{{ $tab==='certificate' ? 'bg-[#004777] text-white' : 'text-slate-500 hover:bg-[#eef8ff] hover:text-[#004777]' }} rounded-xl px-3 py-3 transition">
                    {{ __('general.my_learning.tabs.certificate') }}
                </button>
            </div>
        </div>
    </section>

    <div class="mx-auto w-full max-w-6xl space-y-4 px-4 sm:px-6 lg:px-0">
        <section class="w-full space-y-4">
            {{-- Courses tab content --}}
            @if($tab === 'courses')
            <div class="space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.courses.title') }}</h2>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <div class="relative">
                            <input type="search"
                                   wire:model.live.debounce.300ms="searchCourse"
                                   placeholder="{{ __('general.my_learning.courses.search_placeholder') }}"
                                   class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10 sm:w-56" />
                        </div>

                        <select wire:model.live="filterCourse" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10">
                            <option value="all">{{ __('general.my_learning.courses.filters.all') }}</option>
                            <option value="in_progress">{{ __('general.my_learning.courses.filters.in_progress') }}</option>
                            <option value="completed">{{ __('general.my_learning.courses.filters.completed') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($enrollments as $row)
                        @php
                            $course = $row['enrollment']->course;
                            $courseImage = $course?->poster ?? null;
                            $courseImageSrc = null;

                            if ($courseImage) {
                                if (Str::startsWith($courseImage, ['http://', 'https://'])) {
                                    $courseImageSrc = $courseImage;
                                } else {
                                    if (Str::startsWith($courseImage, 'images/')) {
                                        $courseImageSrc = asset($courseImage);
                                    } else {
                                        $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                                    }
                                }
                            } elseif (!empty($course?->image)) {
                                $courseImageSrc = asset('storage/' . $course->image);
                            }
                        @endphp

                        <a href="{{ localized_route('courses.show', $course?->slug) }}" class="group flex h-full flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:-translate-y-1 hover:border-[#35A7FF]">
                            <div>
                                <div class="relative overflow-hidden rounded-2xl thumb">
                                    @if($courseImageSrc)
                                        <img src="{{ $courseImageSrc }}"
                                             alt="{{ $course?->title }}"
                                             class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20">
                                            <svg width="100" height="56" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="280" height="158" fill="#e6e9ee"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col px-3 pb-3 pt-5">
                                <div class="flex-1 space-y-3">
                                    <div class="space-y-1.5 min-h-[76px]">
                                        <h3 class="line-clamp-2 text-xl font-bold leading-snug text-[#0f172a]">
                                            {{ $course?->title }}
                                        </h3>

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
                        <div class="col-span-full">
                            @if($hasEnrollments)
                                <div class="rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.courses.empty.filtered_title') }}</h3>
                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">
                                        {{ __('general.my_learning.courses.empty.filtered_description') }}
                                    </p>

                                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                                        <button type="button"
                                                wire:click="resetCourseFilters"
                                                class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-medium text-[#004777] transition hover:bg-[#f4faff]">
                                            {{ __('general.my_learning.courses.reset_filters') }}
                                        </button>

                                        <a href="{{ localized_route('courses.index') }}"
                                           class="inline-flex items-center rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#003a5f]">
                                            {{ __('general.my_learning.courses.browse_courses') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-[#004777]">
                                        {{ __('general.my_learning.courses.empty.no_courses_title') }}
                                    </h3>
                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">
                                        {{ __('general.my_learning.courses.empty.no_courses_description') }}
                                    </p>

                                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                                        <a href="{{ localized_route('courses.index') }}"
                                           class="inline-flex items-center rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#003a5f]">
                                            {{ __('general.my_learning.courses.browse_courses') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Session tab content --}}
            @if($tab === 'session')
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.sessions.title') }}</h2>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($upcomingSessions as $session)
                        <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#eef8ff] p-5">
                            <div class="font-bold text-[#004777]">{{ $session->topic?->name }}</div>
                            <div class="mt-2 text-sm text-slate-600">{{ $session->start_at->format('d M Y, H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-[#004777]/70">{{ __('general.my_learning.sessions.empty') }}</div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Certificate tab content --}}
            @if($tab === 'certificate')
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">{{ __('general.my_learning.certificates.title') }}</h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($certificates as $certificate)
                        @php
                            $course = $certificate->resolvedCourse();
                            $courseImage = $course?->poster ?? null;
                            $courseImageSrc = null;

                            if ($courseImage) {
                                if (Str::startsWith($courseImage, ['http://', 'https://'])) {
                                    $courseImageSrc = $courseImage;
                                } else {
                                    if (Str::startsWith($courseImage, 'images/')) {
                                        $courseImageSrc = asset($courseImage);
                                    } else {
                                        $courseImageSrc = asset('images/thumbnail/' . $courseImage);
                                    }
                                }
                            } elseif (!empty($course?->image)) {
                                $courseImageSrc = asset('storage/' . $course->image);
                            }
                        @endphp

                        <article class="group flex h-full flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:-translate-y-1 hover:border-[#35A7FF]">
                            <div>
                                <div class="relative overflow-hidden rounded-2xl thumb">
                                    @if($courseImageSrc)
                                        <img src="{{ $courseImageSrc }}"
                                             alt="{{ $course?->title ?? __('general.my_learning.certificates.default_course_certificate') }}"
                                             class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20">
                                            <svg width="100" height="56" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="280" height="158" fill="#e6e9ee"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>
                                </div>
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
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 3a1 1 0 0 1 1 1v6.586l1.293-1.293a1 1 0 1 1 1.414 1.414l-3 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 1 1 1.414-1.414L9 10.586V4a1 1 0 0 1 1-1Z" />
                                            <path d="M4 13a1 1 0 0 1 1 1v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a1 1 0 1 1 2 0v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-1a1 1 0 0 1 1-1Z" />
                                        </svg>
                                        {{ __('general.my_learning.certificates.download') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full">
                            <div class="rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                                <h3 class="text-base font-semibold text-[#004777]">{{ __('general.my_learning.certificates.empty_title') }}</h3>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">{{ __('general.my_learning.certificates.empty_description') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </section>
    </div>
</div>
