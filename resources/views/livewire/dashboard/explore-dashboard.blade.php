@php
    $featuredCount = $featured->count();
@endphp

<div class="min-h-screen bg-white text-[#0f172a]">
    <section class="relative z-20 isolate overflow-x-clip px-4 pb-20 pt-0 sm:px-6 sm:pb-28 sm:pt-24 lg:px-8">
        <div class="mx-auto grid max-w-6xl items-center gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:gap-4">
            <div class="relative z-10 order-2 text-center lg:order-1 lg:text-left">
                <h1 class="mx-auto max-w-4xl text-4xl font-bold leading-tight tracking-normal text-[#004777] sm:text-6xl lg:mx-0 lg:text-6xl">
                    {{ __('general.explore_dashboard.hero.guest.title') }}
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl lg:mx-0">
                    {{ __('general.explore_dashboard.hero.guest.description') }}
                </p>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <a href="{{ localized_route('courses.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#004777]/15 transition hover:bg-[#00395f]">
                        {{ __('general.explore_dashboard.hero.guest.explore_journey') }}
                    </a>

                    @guest
                        <a href="{{ localized_route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-[#004777]/20 bg-white px-5 py-3 text-sm font-semibold text-[#004777] transition hover:border-[#35A7FF] hover:text-[#00395f]">
                            {{ __('general.explore_dashboard.hero.guest.login') }}
                        </a>
                    @endguest
                </div>

            </div>

            <div class="relative z-10 order-1 flex justify-center lg:order-2 lg:justify-end">
                <div class="absolute inset-[10%] -z-10 rounded-full bg-[#7DD3FC]/55 blur-3xl" aria-hidden="true"></div>
                <span class="pointer-events-none absolute left-[8%] top-[12%] z-0 h-8 w-8 rotate-12 bg-[#FFE100] sm:h-10 sm:w-10"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <span class="pointer-events-none absolute right-[5%] top-[20%] z-0 h-6 w-6 -rotate-12 bg-[#FF8FA3] sm:h-8 sm:w-8"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <span class="pointer-events-none absolute bottom-[28%] left-[2%] z-20 h-5 w-5 rotate-45 bg-[#A78BFA] sm:h-7 sm:w-7"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <span class="pointer-events-none absolute bottom-[18%] right-[2%] z-20 h-7 w-7 -rotate-12 bg-[#38BDF8] sm:h-9 sm:w-9"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <span class="pointer-events-none absolute right-[20%] top-[4%] z-20 hidden h-5 w-5 rotate-45 bg-[#34D399] sm:block sm:h-6 sm:w-6"
                      style="-webkit-mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat; mask: url('{{ asset('images/decor/star.svg') }}') center / contain no-repeat;"
                      aria-hidden="true"></span>
                <img src="{{ asset('images/decor/jesus_christ.png') }}"
                     alt="Jesus teaching children"
                     class="relative z-10 w-full max-w-md drop-shadow-2xl sm:max-w-lg lg:max-w-[38rem]">
            </div>
        </div>

        <img src="{{ asset('images/decor/cloud_1.png') }}"
             alt=""
             class="pointer-events-none absolute -bottom-40 left-16 z-[60] w-64 sm:-bottom-52 sm:left-24 sm:w-96 lg:-bottom-64 lg:left-32 lg:w-[28rem]"
             aria-hidden="true">
    </section>

    <section class="relative z-0 overflow-x-clip px-4 pb-16 pt-16 sm:px-6 sm:pb-24 sm:pt-24 lg:px-8">
        <div class="relative z-10 mx-auto grid max-w-6xl gap-5 md:grid-cols-2">
            <article class="group relative isolate overflow-hidden rounded-[2rem] bg-[#004777] p-7 text-white sm:p-10 md:min-h-[440px]">
                <img src="{{ asset('images/decor/background.png') }}"
                     alt=""
                     class="pointer-events-none absolute inset-0 -z-10 h-full w-full object-cover opacity-10"
                     aria-hidden="true">
                <div class="pointer-events-none absolute -right-16 -top-16 -z-10 h-64 w-64 rounded-full bg-[#35A7FF]/40 blur-3xl" aria-hidden="true"></div>

                <h2 class="max-w-md text-3xl font-bold leading-tight sm:text-5xl md:max-w-[80%]">
                    {{ __('general.explore_dashboard.journey_cards.begin.title') }} <span class="text-[#9fd7ff]">{{ __('general.explore_dashboard.journey_cards.begin.highlight') }}</span>
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-7 text-white/75 sm:text-base md:max-w-[58%]">
                    {{ __('general.explore_dashboard.journey_cards.begin.description') }}
                </p>

                <img src="{{ asset('images/decor/card_1.png') }}"
                     alt="{{ __('general.explore_dashboard.journey_cards.begin.image_alt') }}"
                     class="pointer-events-none mx-auto -mb-14 -translate-y-8 w-[15.5rem] drop-shadow-2xl transition duration-500 group-hover:-translate-y-5 group-hover:translate-x-2 sm:w-[18.25rem] md:absolute md:-bottom-2 md:-right-0 md:mb-0 md:mt-0 md:translate-y-0 md:w-64 md:group-hover:-translate-y-2">
            </article>

            <article class="group relative isolate overflow-hidden rounded-[2rem] bg-[#eef8ff] p-7 sm:p-10 md:min-h-[440px]">
                <img src="{{ asset('images/decor/background.png') }}"
                     alt=""
                     class="pointer-events-none absolute inset-0 -z-10 h-full w-full object-cover opacity-[0.04]"
                     aria-hidden="true">
                <div class="pointer-events-none absolute -bottom-20 -left-16 -z-10 h-72 w-72 rounded-full bg-[#35A7FF]/20 blur-3xl" aria-hidden="true"></div>

                <h2 class="max-w-md text-3xl font-bold leading-tight text-[#0f172a] sm:text-5xl md:max-w-[70%]">
                    {{ __('general.explore_dashboard.journey_cards.learn.title') }} <span class="text-[#168bd9]">{{ __('general.explore_dashboard.journey_cards.learn.highlight') }}</span>
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-600 sm:text-base md:max-w-[58%]">
                    {{ __('general.explore_dashboard.journey_cards.learn.description') }}
                </p>

                <img src="{{ asset('images/decor/card_2.png') }}"
                     alt="{{ __('general.explore_dashboard.journey_cards.learn.image_alt') }}"
                     class="pointer-events-none mx-auto -mb-16 mt-5 -translate-y-8 w-[17rem] drop-shadow-2xl transition duration-500 group-hover:-translate-y-6 group-hover:rotate-2 sm:w-[19.5rem] md:absolute md:-bottom-0 md:-right-2 md:mb-0 md:mt-0 md:translate-y-0 md:w-72 md:group-hover:-translate-y-3">
            </article>

            <article class="group relative isolate overflow-hidden rounded-[2rem] bg-[#f4f1ff] p-7 sm:p-10 md:min-h-[440px]">
                <img src="{{ asset('images/decor/background.png') }}"
                     alt=""
                     class="pointer-events-none absolute inset-0 -z-10 h-full w-full object-cover opacity-[0.04]"
                     aria-hidden="true">
                <div class="pointer-events-none absolute -right-16 -top-16 -z-10 h-64 w-64 rounded-full bg-violet-300/30 blur-3xl" aria-hidden="true"></div>

                <h2 class="max-w-md text-3xl font-bold leading-tight text-[#0f172a] sm:text-5xl md:max-w-[72%]">
                    {{ __('general.explore_dashboard.journey_cards.connect.title') }} <span class="text-violet-600">{{ __('general.explore_dashboard.journey_cards.connect.highlight') }}</span>
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-600 sm:text-base md:max-w-[58%]">
                    {{ __('general.explore_dashboard.journey_cards.connect.description') }}
                </p>

                <img src="{{ asset('images/decor/card_3.png') }}"
                     alt="{{ __('general.explore_dashboard.journey_cards.connect.image_alt') }}"
                     class="pointer-events-none mx-auto -mb-14 mt-5 -translate-y-8 w-[15.5rem] drop-shadow-2xl transition duration-500 group-hover:-translate-y-6 group-hover:-rotate-2 sm:w-[18.25rem] md:absolute md:-bottom-0 md:-right-2 md:mb-0 md:mt-0 md:translate-y-0 md:w-64 md:group-hover:-translate-y-3">
            </article>

            <article class="group relative isolate overflow-hidden rounded-[2rem] bg-[#fff8df] p-7 sm:p-10 md:min-h-[440px]">
                <img src="{{ asset('images/decor/background.png') }}"
                     alt=""
                     class="pointer-events-none absolute inset-0 -z-10 h-full w-full object-cover opacity-[0.04]"
                     aria-hidden="true">
                <div class="pointer-events-none absolute -bottom-20 -left-16 -z-10 h-72 w-72 rounded-full bg-amber-300/30 blur-3xl" aria-hidden="true"></div>

                <h2 class="max-w-md text-3xl font-bold leading-tight text-[#0f172a] sm:text-5xl md:max-w-[75%]">
                    {{ __('general.explore_dashboard.journey_cards.achieve.title') }} <span class="text-amber-600">{{ __('general.explore_dashboard.journey_cards.achieve.highlight') }}</span>
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-600 sm:text-base md:max-w-[58%]">
                    {{ __('general.explore_dashboard.journey_cards.achieve.description') }}
                </p>

                <img src="{{ asset('images/decor/card_4.png') }}"
                     alt="{{ __('general.explore_dashboard.journey_cards.achieve.image_alt') }}"
                     class="pointer-events-none mx-auto -mb-16 mt-5 -translate-y-8 w-[17rem] drop-shadow-2xl transition duration-500 group-hover:-translate-y-6 group-hover:rotate-2 sm:w-[19.5rem] md:absolute md:-bottom-7 md:-right-2 md:mb-0 md:mt-0 md:translate-y-0 md:w-72 md:group-hover:-translate-y-3">
            </article>
        </div>

        <img src="{{ asset('images/decor/cloud_2.png') }}"
             alt=""
             class="pointer-events-none absolute -bottom-28 right-4 z-20 w-64 sm:-bottom-36 sm:right-8 sm:w-96 lg:-bottom-48 lg:right-16 lg:w-[30rem]"
             aria-hidden="true">
    </section>

    <section id="community" class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="relative isolate mx-auto grid max-w-6xl items-center gap-8 overflow-hidden rounded-[2rem] bg-[#eef8ff] px-7 py-10 sm:px-10 sm:py-14 lg:grid-cols-[1.05fr_0.95fr] lg:px-14 lg:py-16">
            {{-- <img src="{{ asset('images/decor/background.png') }}"
                 alt=""
                 class="pointer-events-none absolute inset-0 -z-20 h-full w-full object-cover opacity-[0.04]"
                 aria-hidden="true"> --}}
            <div class="pointer-events-none absolute -left-24 -top-24 -z-10 h-72 w-72 rounded-full bg-[#7DD3FC]/45 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 right-10 -z-10 h-80 w-80 rounded-full bg-violet-300/30 blur-3xl" aria-hidden="true"></div>

            <div class="relative z-10">
                <h2 class="max-w-2xl text-3xl font-bold leading-tight text-[#004777] sm:text-5xl">
                    {{ __('general.explore_dashboard.cta.title') }}
                </h2>
                <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                    {{ __('general.explore_dashboard.cta.description') }}
                </p>

                <ul class="mt-8 flex flex-wrap gap-3">
                    <li class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2.5 text-sm font-semibold text-[#004777] shadow-sm">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#35A7FF]/15">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('general.explore_dashboard.cta.learn.description') }}
                    </li>
                    <li class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2.5 text-sm font-semibold text-[#004777] shadow-sm">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-600">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('general.explore_dashboard.cta.disciple.description') }}
                    </li>
                    <li class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2.5 text-sm font-semibold text-[#004777] shadow-sm">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('general.explore_dashboard.cta.community.description') }}
                    </li>
                </ul>

                <a href="{{ localized_route('courses.index') }}"
                   class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#004777]/15 transition hover:-translate-y-0.5 hover:bg-[#00395f]">
                    {{ __('general.explore_dashboard.cta.start_your_journey') }}
                </a>
            </div>

            <div class="relative flex min-h-72 items-center justify-center sm:min-h-96 lg:min-h-[28rem] lg:justify-end">
                <img src="{{ asset('images/decor/book_1.png') }}"
                     alt="{{ __('general.explore_dashboard.cta.learn.description') }}"
                     class="relative z-10 w-full max-w-sm drop-shadow-2xl transition duration-500 hover:-translate-y-2 sm:max-w-md lg:max-w-lg">
            </div>
        </div>
    </section>

    @if($featuredCount)
        <section id="impact" class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mx-auto mb-12 max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-[#0f172a] sm:text-5xl">{{ __('general.explore_dashboard.featured_teachings.title') }}</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">{{ __('general.explore_dashboard.featured_teachings.description') }}</p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    @foreach($featured->take(3) as $course)
                        @php
                            $courseImage = $course->poster ?? null;
                            $courseImageSrc = null;
                            if ($courseImage) {
                                if (\Illuminate\Support\Str::startsWith($courseImage, ['http://', 'https://'])) {
                                    $courseImageSrc = $courseImage;
                                } elseif ($thumbnailUrl = course_thumbnail_url($courseImage)) {
                                    $courseImageSrc = $thumbnailUrl;
                                } else {
                                    $courseImageSrc = course_thumbnail_url($courseImage);
                                }
                            }
                        @endphp

                        <a href="{{ localized_route('courses.show', $course->slug) }}"
                           class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:-translate-y-0.5 hover:border-[#35A7FF]">
                            @if($courseImageSrc)
                                <img src="{{ $courseImageSrc }}" alt="{{ $course->title }}" class="h-44 w-full rounded-xl object-cover">
                            @else
                                <div class="flex h-44 w-full items-center justify-center rounded-xl bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/10 text-[#004777]">
                                    {{ __('general.explore_dashboard.featured_teachings.title') }}
                                </div>
                            @endif

                            <div class="px-3 pb-3 pt-5">
                                <h3 class="mt-2 text-lg font-bold leading-snug text-[#0f172a]">{{ \Illuminate\Support\Str::limit($course->title, 80) }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $course->instructor?->name ?? $course->author ?? __('general.explore_dashboard.defaults.instructor') }}
                                </p>

                                <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#004777]">
                                    {{ __('general.explore_dashboard.featured_teachings.open') }}
                                    <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="relative mx-auto max-w-6xl rounded-[2rem] border border-[#35A7FF]/20 bg-[#eef8ff] px-7 py-12 text-center sm:px-12 sm:py-16">
            <img src="{{ asset('images/decor/cta_deco.png') }}" alt=""
                 class="pointer-events-none absolute -left-12 -top-12 z-20 w-30 rotate-[-20deg] sm:-left-12 sm:-top-12 sm:w-32 lg:-left-20 lg:-top-20 lg:w-42"
                 aria-hidden="true">

            <div class="relative z-10">
                <img src="{{ asset('images/decor/cta.png') }}"
                     alt="{{ __('general.explore_dashboard.final_cta.image_alt') }}"
                     class="mx-auto mb-6 w-36 drop-shadow-xl sm:w-44">

                <h2 class="mx-auto max-w-3xl text-3xl font-bold leading-tight text-[#004777] sm:text-5xl">
                    {{ __('general.explore_dashboard.final_cta.title') }}
                </h2>
                {{-- <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-xl">
                    {{ __('general.explore_dashboard.final_cta.description') }}
                </p> --}}
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ localized_route('courses.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#004777]/15 transition hover:-translate-y-0.5 hover:bg-[#00395f]">
                        {{ __('general.explore_dashboard.final_cta.button') }}
                    </a>
                    @guest
                        <a href="{{ localized_route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-[#004777]/20 bg-white px-5 py-3 text-sm font-semibold text-[#004777] transition hover:-translate-y-0.5 hover:border-[#35A7FF]">
                            {{ __('general.explore_dashboard.hero.guest.login') }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>
</div>
