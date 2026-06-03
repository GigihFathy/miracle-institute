@php
    use Illuminate\Support\Str;

    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="px-4 py-10 text-mentor-primary sm:px-6 lg:px-10 xl:px-16 2xl:px-24">
    <div class="space-y-8">
        <section class="space-y-6">
            <div class="mx-auto space-y-3">
                <h2 class="text-2xl font-semibold tracking-tight text-mentor-primary sm:text-3xl lg:text-[2.75rem]">
                    {{ app()->getLocale() === 'id' ? 'Jelajahi Course Online yang Menginspirasi' : 'Explore Inspiring Online Courses' }}
                </h2>

                {{-- <p class="mx-auto text-sm leading-7 text-[color:color-mix(in_oklab,#004777_72%,white)] sm:text-[15px]">
                    {{ app()->getLocale() === 'id' ? 'Temukan course yang tepat untuk langkah belajar berikutnya dan pilih program yang paling sesuai untuk perjalananmu.' : 'Find the right course for your next learning step and choose the program that best fits your journey.' }}
                </p> --}}
            </div>

            <div class="flex max-w-4xl flex-col gap-3 sm:flex-row">
                <form wire:submit="submitSearch" class="relative w-full">
                    <input type="search"
                           wire:model.live.debounce.300ms="searchInput"
                           placeholder="{{ __('general.course_catalog.filters.search_placeholder') }}"
                           class="h-12 w-full rounded-full border border-slate-200 bg-white pl-5 pr-12 text-sm text-mentor-primary outline-none transition placeholder:text-slate-400 focus:border-mentor-primary focus:ring-2 focus:ring-mentor-secondary-solid">

                    <button type="submit"
                            class="absolute right-1.5 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-mentor-primary text-white transition hover:opacity-90">
                        <span class="sr-only">{{ __('general.topbar_search.submit') }}</span>
                        <svg class="h-4 w-4"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.5">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </button>
                </form>

                <select wire:model.live="sort"
                        class="h-12 rounded-full border border-slate-200 bg-white px-5 text-sm text-mentor-primary outline-none transition focus:border-mentor-primary focus:ring-2 focus:ring-mentor-secondary-solid sm:w-48">
                    <option value="newest">{{ app()->getLocale() === 'id' ? 'Terbaru' : 'Newest' }}</option>
                    <option value="oldest">{{ app()->getLocale() === 'id' ? 'Terlama' : 'Oldest' }}</option>
                </select>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="button"
                        wire:click="$set('studyProgram', '')"
                        @class([
                            'inline-flex min-h-11 items-center rounded-full border px-5 py-2.5 text-sm font-semibold transition',
                            'border-mentor-primary bg-mentor-primary text-white' => $studyProgram === '',
                            'border-slate-300 bg-white text-mentor-primary hover:border-mentor-primary/40 hover:bg-mentor-primary-soft-2' => $studyProgram !== '',
                        ])>
                    {{ __('general.course_catalog.filters.all_study_programs') }}
                </button>

                @foreach($studyPrograms as $sp)
                    <button type="button"
                            wire:click="$set('studyProgram', '{{ $sp->slug }}')"
                            @class([
                                'inline-flex min-h-11 items-center rounded-full border px-5 py-2.5 text-sm font-semibold transition',
                                'border-mentor-primary bg-mentor-primary text-white' => $studyProgram === $sp->slug,
                                'border-slate-300 bg-white text-mentor-primary hover:border-mentor-primary/40 hover:bg-mentor-primary-soft-2' => $studyProgram !== $sp->slug,
                            ])>
                        {{ $sp->title }}
                    </button>
                @endforeach
            </div>

            <div class="text-sm text-slate-500">
                {{ $courses->total() }} {{ Str::plural(app()->getLocale() === 'id' ? 'course' : 'course', $courses->total()) }}
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            @forelse($courses as $course)
                @php
                    $enrolled = in_array($course->id, $enrolledCourseIds, true);
                    $courseImage = $course->poster ?? null;
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
                    } elseif (!empty($course->image)) {
                        $courseImageSrc = asset('storage/' . $course->image);
                    }
                @endphp

                <article class="group relative h-full overflow-hidden rounded-2xl transition-colors hover:bg-gray-200">
                    <a href="{{ localized_route('courses.show', $course->slug) }}"
                       aria-label="{{ __('general.course_catalog.actions.open') }}: {{ $course->title }}"
                       class="absolute inset-0 z-10 rounded-2xl focus:outline-none focus:ring-2 focus:ring-mentor-secondary-solid focus:ring-offset-2 focus:ring-offset-white"></a>

                    <div class="h-full p-2.5 transition-colors group-hover:bg-gray-200">
                        <div class="overflow-hidden rounded-lg thumb">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}"
                                     alt="{{ $course->title }}"
                                     class="h-36 w-full object-cover sm:h-40">
                            @else
                                <div class="flex h-36 w-full items-center justify-center bg-slate-200 sm:h-40">
                                    <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="280" height="158" fill="#e6e9ee"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="relative z-20 mt-3 flex h-[calc(100%-9rem)] flex-col pointer-events-none sm:h-[calc(100%-10rem)]">
                            <div class="flex-1">
                                <div class="text-[11px] uppercase tracking-wide text-[#3B82F6]/70">
                                    {{ $course->studyProgram?->title }}
                                </div>

                                <h3 class="mt-1 line-clamp-2 text-sm font-semibold leading-tight text-[#004777]">
                                    {{ \Illuminate\Support\Str::limit($course->title, 72) }}
                                </h3>

                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-[#004777]/70">
                                    {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="rounded px-2 py-0.5 text-[11px] bg-[#3B82F6]/10 text-[#004777]">
                                        {{ trans_choice('general.course_catalog.badges.topics', $course->topics_count, ['count' => $course->topics_count]) }}
                                    </span>

                                    @if($enrolled)
                                        <span class="rounded px-2 py-0.5 text-[11px] bg-emerald-50 text-emerald-700">
                                            {{ __('general.course_catalog.badges.enrolled') }}
                                        </span>
                                    @elseif($isMentor)
                                        <span class="rounded px-2 py-0.5 text-[11px] bg-slate-100 text-slate-600">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                            @if(!$isMentor)
                                @auth
                                    @unless($enrolled)
                                        <button wire:click="enroll('{{ $course->id }}')"
                                                class="admin-neutral-button pointer-events-auto relative z-20 inline-flex items-center justify-center rounded-full px-4 py-2 text-xs font-medium">
                                            {{ __('general.course_catalog.actions.enroll') }}
                                        </button>
                                    @endunless
                                @endauth
                            @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full">
                    <div class="px-8 py-16 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-mentor-primary-soft-2">
                            <svg class="h-7 w-7 text-white"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M9.75 9.75h4.5m-4.5 4.5h4.5M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-semibold text-mentor-primary">
                            {{ __('general.course_catalog.empty.title') }}
                        </h3>

                        <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_72%,white)]">
                            {{ __('general.course_catalog.empty.description') }}
                        </p>
                    </div>
                </div>
            @endforelse
        </section>

        <div class="course-catalog-pagination pt-2">
            {{ $courses->links() }}
        </div>
    </div>
</div>
