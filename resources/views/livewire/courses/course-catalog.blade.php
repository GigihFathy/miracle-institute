@php
    use Illuminate\Support\Str;

    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-6 origin-top lg:px-20 2xl:px-28">
    <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)]">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <div class="text-xs uppercase tracking-[0.18em] text-[color:color-mix(in_oklab,#004777_48%,white)]">
                    {{ app()->getLocale() === 'id' ? 'Katalog pembelajaran' : 'Learning catalog' }}
                </div>
                <h2 class="mt-2 text-xl font-bold tracking-tight text-mentor-primary sm:text-2xl">
                    {{ app()->getLocale() === 'id' ? 'Temukan course yang tepat untuk langkah belajar berikutnya' : 'Find the right course for your next learning step' }}
                </h2>
            </div>

            <div class="text-sm text-slate-500">
                {{ $courses->total() }} {{ Str::plural(app()->getLocale() === 'id' ? 'course' : 'course', $courses->total()) }}
            </div>
        </div>

        <div class="mt-5 grid gap-3 xl:grid-cols-[1fr_260px_180px]">
            <div class="min-w-0">
                <input type="search"
                       wire:model.live.debounce.500ms="search"
                       placeholder="{{ __('general.course_catalog.filters.search_placeholder') }}"
                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 text-sm text-mentor-primary outline-none transition focus:border-mentor-primary focus:bg-white focus:ring-2 focus:ring-mentor-secondary-solid">
            </div>

            <select wire:model.live="studyProgram"
                    class="h-12 rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 px-4 text-sm text-mentor-primary outline-none transition focus:border-mentor-primary focus:bg-white focus:ring-2 focus:ring-mentor-secondary-solid">
                <option value="">{{ __('general.course_catalog.filters.all_study_programs') }}</option>

                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->slug }}">
                        {{ $sp->title }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="sort"
                    class="h-12 rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 px-4 text-sm text-mentor-primary outline-none transition focus:border-mentor-primary focus:bg-white focus:ring-2 focus:ring-mentor-secondary-solid">
                <option value="newest">{{ app()->getLocale() === 'id' ? 'Terbaru' : 'Newest' }}</option>
                <option value="oldest">{{ app()->getLocale() === 'id' ? 'Terlama' : 'Oldest' }}</option>
            </select>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
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

            <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_25px_rgba(15,23,42,0.04)] transition hover:-translate-y-1 hover:border-slate-300 hover:shadow-[0_18px_35px_rgba(15,23,42,0.08)]">
                <div class="p-3">
                    <div class="relative overflow-hidden rounded-2xl">
                        @if($courseImageSrc)
                            <img src="{{ $courseImageSrc }}"
                                 alt="{{ $course->title }}"
                                 class="h-40 w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-40 w-full items-center justify-center bg-slate-200">
                                <svg width="100" height="56" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="280" height="158" fill="#e6e9ee"/>
                                </svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/45 via-slate-950/10 to-transparent"></div>

                        <div class="absolute left-3 top-3">
                            <span class="inline-flex rounded-full border border-white/20 bg-white/15 px-2.5 py-1 text-[10px] font-medium text-white backdrop-blur">
                                {{ $course->studyProgram?->title }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-1 flex-col px-5 pb-5">
                    <div class="flex-1 space-y-3">
                        <div class="min-h-[84px] space-y-2">
                            <h3 class="line-clamp-2 text-[17px] font-bold leading-snug text-mentor-primary">
                                {{ $course->title }}
                            </h3>

                            <p class="line-clamp-3 text-sm leading-6 text-[color:color-mix(in_oklab,#004777_72%,white)]">
                                {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 text-[11px]">
                            <span class="rounded-full border border-slate-200 bg-mentor-primary-soft-2 px-2.5 py-1 text-mentor-primary">
                                {{ trans_choice('general.course_catalog.badges.topics', $course->topics_count, ['count' => $course->topics_count]) }}
                            </span>

                            @if($isMentor)
                                <span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-slate-700">
                                    {{ ucfirst($course->status) }}
                                </span>
                            @endif

                            @if($enrolled)
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">
                                    {{ __('general.course_catalog.badges.enrolled') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-2">
                        <a href="{{ localized_route('courses.show', $course->slug) }}"
                           class="admin-primary-button inline-flex flex-1 items-center justify-center rounded-xl px-4 py-2.5 text-sm">
                            {{ __('general.course_catalog.actions.open') }}
                        </a>

                        @if(!$isMentor)
                            @auth
                                @unless($enrolled)
                                    <button wire:click="enroll('{{ $course->id }}')"
                                            class="admin-neutral-button inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm">
                                        {{ __('general.course_catalog.actions.enroll') }}
                                    </button>
                                @endunless
                            @endauth
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full">
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-8 py-20 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-mentor-primary-soft-2">
                        <svg class="h-8 w-8 text-mentor-primary"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M9.75 9.75h4.5m-4.5 4.5h4.5M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"/>
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-mentor-primary">
                        {{ __('general.course_catalog.empty.title') }}
                    </h3>

                    <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_72%,white)]">
                        {{ __('general.course_catalog.empty.description') }}
                    </p>
                </div>
            </div>
        @endforelse
    </section>

    <div class="pt-2">
        {{ $courses->links() }}
    </div>
</div>
