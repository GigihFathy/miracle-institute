<footer class="bg-[#004777] text-slate-300">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-[1.35fr_1fr_0.8fr] lg:gap-14 lg:px-8 lg:py-16">

        <div class="space-y-4">
            @php $company = \App\Models\Company::first(); @endphp

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
                <li><a href="{{ localized_route('explore.dashboard') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">{{ __('general.navigation.dashboard') }}</a></li>
                <li><a href="{{ localized_route('courses.index') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">{{ __('general.navigation.courses') }}</a></li>

                @auth
                    <li><a href="{{ localized_route('learning.dashboard') }}" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/10 hover:text-white">{{ __('general.navigation.my_learning') }}</a></li>
                @endauth
            </ul>
        </div>

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

                @if(filled($company?->whatsapp))
                    <a href="https://wa.me/{{ $company->whatsapp }}" target="_blank"
                    aria-label="{{ __('general.footer.contact.whatsapp') }}"
                    class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-lg transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/20 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.121 1.532 5.849L.057 23.535a.75.75 0 0 0 .916.932l5.853-1.53A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.713 9.713 0 0 1-4.953-1.355l-.355-.21-3.676.961.983-3.584-.229-.368A9.712 9.712 0 0 1 2.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/>
                        </svg>
                    </a>
                @endif
            </div>

        </div>

    </div>

    <div class="border-t border-white/10 px-4 sm:px-6">
        <div class="mx-auto flex max-w-6xl items-center justify-center py-5 text-center text-xs text-slate-400">
            <span>{{ __('general.footer.copyright', ['year' => date('Y')]) }}</span>
        </div>
    </div>

</footer>
