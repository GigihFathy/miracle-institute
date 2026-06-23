@php
    $company = \App\Models\Company::first();
    $hasSocialLinks = filled($company?->facebook)
        || filled($company?->instagram)
        || filled($company?->youtube)
        || filled($company?->email);
@endphp

<footer class="bg-[#004777] text-slate-300">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 {{ $hasSocialLinks ? 'lg:grid-cols-[1.35fr_1fr_0.8fr]' : 'lg:grid-cols-[1.35fr_1fr]' }} lg:gap-14 lg:px-8 lg:py-16">

        <div class="space-y-4">

            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-white">
                    {{ $company?->name ?? config('app.name') }}
                </span>
            </div>

            <p class="max-w-md text-sm leading-7 text-slate-300/80">
                {{ $company?->description ?? __('general.footer.default_description') }}
            </p>

            @if(filled($company?->vision))
                <p class="max-w-md border-l-2 border-[#35A7FF] pl-3 text-xs italic leading-6 text-slate-400">
                    "{{ $company->vision }}"
                </p>
            @endif
        </div>

        <div>
            <h3 class="mb-5 text-sm font-bold uppercase tracking-[0.18em] text-white">{{ __('general.footer.navigation.title') }}</h3>
            <ul class="grid gap-2 text-sm sm:grid-cols-2 lg:grid-cols-1">
                <li><a href="{{ localized_route('explore.dashboard') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">Home</a></li>
                <li><a href="{{ localized_route('courses.index') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">{{ __('general.navigation.courses') }}</a></li>
                <li><a href="{{ localized_route('legal.terms') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">Terms &amp; Conditions</a></li>
                <li><a href="{{ localized_route('legal.privacy') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">Privacy Policy</a></li>

                @auth
                    <li><a href="{{ localized_route('learning.dashboard') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">{{ __('general.navigation.my_learning') }}</a></li>
                @endauth
            </ul>
        </div>

        @if($hasSocialLinks)
            <div>
                <h3 class="mb-5 text-sm font-bold uppercase tracking-[0.18em] text-white">{{ __('general.footer.social.title') }}</h3>

                <div class="flex flex-wrap gap-3">
                    @if(filled($company?->facebook))
                        <a href="{{ $company->facebook }}" target="_blank"
                           aria-label="Facebook"
                           class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-lg transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/20 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                <path d="M22 12a10 10 0 10-11.63 9.88v-6.99H7.9V12h2.47V9.8c0-2.43 1.45-3.78 3.67-3.78 1.06 0 2.17.19 2.17.19v2.39h-1.22c-1.2 0-1.57.74-1.57 1.5V12h2.67l-.43 2.89h-2.24v6.99A10 10 0 0022 12z"/>
                            </svg>
                        </a>
                    @endif

                    @if(filled($company?->instagram))
                        <a href="{{ $company->instagram }}" target="_blank"
                           aria-label="{{ __('general.footer.contact.instagram') }}"
                           class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-lg transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/20 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                <path d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5c3.18 0 5.75-2.57 5.75-5.75v-8.5C22 4.57 19.43 2 16.25 2h-8.5zm0 2h8.5A3.75 3.75 0 0120 7.75v8.5A3.75 3.75 0 0116.25 20h-8.5A3.75 3.75 0 014 16.25v-8.5A3.75 3.75 0 017.75 4zm4.25 2.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zm0 2a3.5 3.5 0 110 7 3.5 3.5 0 010-7zm4.75-.75a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5z"/>
                            </svg>
                        </a>
                    @endif

                    @if(filled($company?->youtube))
                        <a href="{{ $company->youtube }}" target="_blank"
                           aria-label="YouTube"
                           class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-lg transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/20 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                <path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 00.5 6.2 31.4 31.4 0 000 12a31.4 31.4 0 00.5 5.8 3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1A31.4 31.4 0 0024 12a31.4 31.4 0 00-.5-5.8zM9.6 15.5v-7l6.4 3.5-6.4 3.5z"/>
                            </svg>
                        </a>
                    @endif

                    @if(filled($company?->email))
                        <a href="mailto:{{ $company->email }}"
                           aria-label="{{ __('general.footer.contact.email') }}"
                           class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-lg transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/20 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                                <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <div class="border-t border-white/10 px-4 sm:px-6">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-center gap-2 py-5 text-center text-xs text-slate-400">
            <span>{{ __('general.footer.copyright', ['year' => date('Y')]) }}</span>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ localized_route('legal.terms') }}" class="underline underline-offset-4 hover:text-white">Terms &amp; Conditions</a>
                <a href="{{ localized_route('legal.privacy') }}" class="underline underline-offset-4 hover:text-white">Privacy Policy</a>
            </div>
        </div>
    </div>

</footer>
