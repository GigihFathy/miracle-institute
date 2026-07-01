@php
    use Illuminate\Support\Str;

    $isMentor = session('active_role') === 'disciples';
    $hero = $isMentor ? 'mentor' : 'student';
@endphp

<div class="min-h-screen bg-white text-[#0f172a]">
    <section class="relative isolate overflow-x-clip px-4 pb-16 pt-10 sm:px-6 sm:pb-20 sm:pt-16 lg:px-8">
        <div class="relative mx-auto grid max-w-6xl items-center gap-4 overflow-hidden rounded-[2rem] bg-[#eef8ff] px-7 py-8 sm:gap-8 sm:px-10 sm:py-14 lg:grid-cols-[1.1fr_0.9fr] lg:px-14 lg:py-16">
            <div class="pointer-events-none absolute -left-24 -top-24 -z-10 h-72 w-72 rounded-full bg-[#7DD3FC]/50 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 right-10 -z-10 h-80 w-80 rounded-full bg-violet-300/30 blur-3xl" aria-hidden="true"></div>

            <div class="relative z-10 order-2 text-center lg:order-1 lg:text-left">
                <h1 class="text-xl font-bold leading-tight text-[#004777] sm:text-5xl lg:text-6xl">
                    {{ __("general.course_catalog.hero.{$hero}.title") }}
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-slate-600 sm:mt-5 sm:text-lg sm:leading-7 lg:mx-0">
                    {{ __("general.course_catalog.hero.{$hero}.description") }}
                </p>
            </div>

            <div class="relative order-1 flex min-h-52 items-center justify-center lg:order-2 lg:min-h-[24rem] lg:justify-end">
                <span class="pointer-events-none absolute left-[8%] top-[12%] h-7 w-7 rotate-12 bg-[#FFE100]"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <span class="pointer-events-none absolute right-[8%] top-[18%] h-6 w-6 -rotate-12 bg-[#FF8FA3]"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <img src="{{ asset('images/decor/book_1.png') }}"
                     alt="{{ __('general.course_catalog.stats.available_courses') }}"
                     class="relative z-10 w-full max-w-[12rem] drop-shadow-2xl sm:max-w-sm lg:max-w-md">
            </div>
        </div>
    </section>

    <section class="px-4 pb-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl rounded-[2rem] border border-[#35A7FF]/15 bg-white p-5 sm:p-7">
            <div class="flex flex-col gap-3 lg:flex-row">
                <form wire:submit="submitSearch" class="relative flex-1">
                    <input type="search"
                           wire:model.live.debounce.300ms="searchInput"
                           placeholder="{{ __('general.course_catalog.filters.search_placeholder') }}"
                           class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-5 pr-14 text-sm text-[#004777] outline-none transition placeholder:text-slate-400 focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10">

                    <button type="submit"
                            class="absolute right-1.5 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-lg bg-[#004777] text-white transition hover:bg-[#00395f]">
                        <span class="sr-only">{{ __('general.topbar_search.submit') }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>

                <select wire:model.live="sort"
                        class="h-12 rounded-xl border border-slate-200 bg-slate-50 px-5 text-sm font-semibold text-[#004777] outline-none transition focus:border-[#35A7FF] focus:bg-white focus:ring-4 focus:ring-[#35A7FF]/10 lg:w-52">
                    <option value="newest">{{ __('general.course_catalog.sort.newest') }}</option>
                    <option value="oldest">{{ __('general.course_catalog.sort.oldest') }}</option>
                </select>
            </div>

        </div>
    </section>

    <section class="px-4 pb-16 sm:px-6 sm:pb-24 lg:px-8">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex items-center justify-between gap-4">
                <h2 class="text-2xl font-bold text-[#004777] sm:text-3xl">
                    {{ __('general.course_catalog.stats.available_courses') }}
                </h2>
                <span class="rounded-full bg-[#eef8ff] px-4 py-2 text-sm font-semibold text-[#004777]">
                    {{ $courses->total() }}
                </span>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($courses as $course)
                    @php
                        $enrolled = in_array($course->id, $enrolledCourseIds, true);
                        $courseImage = $course->poster ?? null;
                        $courseImageSrc = null;

                        if ($courseImage) {
                            if (Str::startsWith($courseImage, ['http://', 'https://'])) {
                                $courseImageSrc = $courseImage;
                            } elseif ($thumbnailUrl = course_thumbnail_url($courseImage)) {
                                $courseImageSrc = $thumbnailUrl;
                            } else {
                                $courseImageSrc = course_thumbnail_url($courseImage);
                            }
                        } elseif (!empty($course->image)) {
                            $courseImageSrc = asset('storage/' . $course->image);
                        }
                    @endphp

                    <article class="group relative flex h-full flex-col rounded-[1.5rem] border border-slate-200 bg-white p-3 transition hover:border-[#35A7FF]">
                        <a href="{{ localized_route('courses.show', $course->slug) }}"
                           aria-label="{{ __('general.course_catalog.actions.open') }}: {{ $course->title }}"
                           class="absolute inset-0 z-10 rounded-[1.5rem] focus:outline-none focus:ring-2 focus:ring-[#35A7FF] focus:ring-offset-2"></a>

                        <div class="relative overflow-hidden rounded-2xl">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}"
                                     alt="{{ $course->title }}"
                                     class="h-52 w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-52 w-full items-center justify-center bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20 text-[#004777]">
                                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                        <path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20v3H6.5A2.5 2.5 0 0 1 4 18.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            @endif

                            @if($enrolled && !$isMentor)
                                <span class="absolute left-3 top-3 rounded-full bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white">
                                    {{ __('general.course_catalog.badges.enrolled') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col px-3 pb-3 pt-5">
                            <h3 class="mt-2 line-clamp-2 text-xl font-bold leading-snug text-[#0f172a]">
                                {{ Str::limit($course->title, 80) }}
                            </h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                                {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                            </p>

                            <div class="relative z-20 mt-auto flex items-center justify-between gap-3 pt-5 pointer-events-none">
                                <span class="rounded-full bg-[#eef8ff] px-3 py-1.5 text-xs font-semibold text-[#004777]">
                                    {{ trans_choice('general.course_catalog.badges.topics', $course->topics_count, ['count' => $course->topics_count]) }}
                                </span>

                                @if(!$isMentor)
                                    @auth
                                        @unless($enrolled)
                                            <button wire:click="confirmEnroll('{{ $course->id }}')"
                                                    class="pointer-events-auto relative z-20 inline-flex items-center justify-center rounded-full bg-[#004777] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#00395f]">
                                                {{ __('general.course_catalog.actions.enroll') }}
                                            </button>
                                        @endunless
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-[2rem] bg-[#eef8ff] px-8 py-16 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-[#004777]">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75h4.5m-4.5 4.5h4.5M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"/>
                            </svg>
                        </div>
                        <h3 class="mt-5 text-xl font-bold text-[#004777]">
                            {{ __('general.course_catalog.empty.title') }}
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">
                            {{ __('general.course_catalog.empty.description') }}
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="course-catalog-pagination pt-10">
                {{ $courses->links() }}
            </div>
        </div>
    </section>

    @if($showEnrollModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <button type="button" class="absolute inset-0" wire:click="closeEnrollModal" aria-label="Tutup modal konfirmasi"></button>

            <div class="relative z-10 w-full max-w-md rounded-[1rem] border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="space-y-3">
                    <div>
                        <h3 class="text-xl font-bold text-[#004777]">Konfirmasi pendaftaran topik pembelajaran</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Kamu yakin ingin mendaftar ke topik pembelajaran
                            <span class="font-semibold text-[#004777]">{{ $pendingCourseTitle }}</span>?
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeEnrollModal"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="enroll"
                        wire:loading.attr="disabled"
                        wire:target="enroll"
                        class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#00395f] disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="enroll">Ya, daftar sekarang</span>
                        <span wire:loading.inline-flex wire:target="enroll" class="items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
