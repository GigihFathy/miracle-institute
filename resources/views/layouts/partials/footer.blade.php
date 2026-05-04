<footer class="bg-slate-900 text-slate-300 mt-16">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- BRAND -->
        <div class="space-y-4">
            @php $company = \App\Models\Company::first(); @endphp

            <div class="flex items-center gap-3">
                @if($company?->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}"
                         class="h-10 w-10 object-contain">
                @endif
                <span class="text-white font-semibold text-lg">
                    {{ $company?->name ?? config('app.name') }}
                </span>
            </div>

            <p class="text-sm text-slate-400 leading-relaxed">
                {{ $company?->description ?? 'Platform pembelajaran profesional untuk meningkatkan kompetensi dan karier Anda.' }}
            </p>

            @if($company?->vision)
                <p class="text-xs text-slate-500 italic">
                    "{{ $company->vision }}"
                </p>
            @endif
        </div>

        <!-- NAVIGATION -->
        <div>
            <h3 class="text-white font-semibold mb-4">Navigation</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('explore.dashboard') }}" class="hover:text-white">Dashboard</a></li>
                <li><a href="{{ route('courses.index') }}" class="hover:text-white">Courses</a></li>
                <li><a href="{{ route('articles.index') }}" class="hover:text-white">Articles</a></li>

                @auth
                    <li><a href="{{ route('learning.dashboard') }}" class="hover:text-white">My Learning</a></li>
                    <li><a href="{{ route('certificates.index') }}" class="hover:text-white">Certificates</a></li>
                @endauth
            </ul>
        </div>

        <!-- CONTACT -->
        <div>
            <h3 class="text-white font-semibold mb-4">Contact</h3>
            <ul class="space-y-2 text-sm text-slate-400">

                @if($company?->address)
                    <li>{{ $company->address }}</li>
                @endif

                @if($company?->whatsapp)
                    <li>
                        <a href="https://wa.me/{{ $company->whatsapp }}"
                           target="_blank"
                           class="hover:text-white">
                            WhatsApp
                        </a>
                    </li>
                @endif

                @if($company?->instagram)
                    <li>
                        <a href="{{ $company->instagram }}"
                           target="_blank"
                           class="hover:text-white">
                            Instagram
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- SOCIAL -->
        <div>
            <h3 class="text-white font-semibold mb-4">Follow Us</h3>

            <div class="flex gap-3">
                @if($company?->facebook)
                    <a href="{{ $company->facebook }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M22 12a10 10 0 10-11.63 9.88v-6.99H7.9V12h2.47V9.8c0-2.43 1.45-3.78 3.67-3.78 1.06 0 2.17.19 2.17.19v2.39h-1.22c-1.2 0-1.57.74-1.57 1.5V12h2.67l-.43 2.89h-2.24v6.99A10 10 0 0022 12z"/>
                        </svg>
                    </a>
                @endif

                @if($company?->instagram)
                    <a href="{{ $company->instagram }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5c3.18 0 5.75-2.57 5.75-5.75v-8.5C22 4.57 19.43 2 16.25 2h-8.5zm0 2h8.5A3.75 3.75 0 0120 7.75v8.5A3.75 3.75 0 0116.25 20h-8.5A3.75 3.75 0 014 16.25v-8.5A3.75 3.75 0 017.75 4zm4.25 2.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zm0 2a3.5 3.5 0 110 7 3.5 3.5 0 010-7zm4.75-.75a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5z"/>
                        </svg>
                    </a>
                @endif

                @if($company?->youtube)
                    <a href="{{ $company->youtube }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 00.5 6.2 31.4 31.4 0 000 12a31.4 31.4 0 00.5 5.8 3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1A31.4 31.4 0 0024 12a31.4 31.4 0 00-.5-5.8zM9.6 15.5v-7l6.4 3.5-6.4 3.5z"/>
                        </svg>
                    </a>
                @endif

                @if($company?->whatsapp)
                    <a href="https://wa.me/{{ $company->whatsapp }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.8 11.8 0 0012.01 0 11.94 11.94 0 001.5 17.72L0 24l6.44-1.68A11.92 11.92 0 0012 24h.01a11.94 11.94 0 008.51-20.52zM12 21.82a9.8 9.8 0 01-5-1.37l-.36-.21-3.83 1 1.02-3.74-.23-.38a9.8 9.8 0 1116.4 3.16A9.7 9.7 0 0112 21.82zm5.54-7.37c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.69.15s-.79.98-.97 1.18c-.18.2-.36.23-.66.08-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.68-2.08-.18-.3-.02-.46.13-.61.13-.13.3-.34.45-.51.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.69-1.66-.94-2.27-.24-.58-.48-.5-.66-.51h-.56c-.2 0-.53.08-.8.38s-1.05 1.03-1.05 2.5 1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.17 4.56.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.18-1.44-.08-.13-.28-.2-.58-.35z"/>
                        </svg>
                    </a>
                @endif
            </div>

        </div>

    </div>

    <!-- BOTTOM -->
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-center items-center text-xs text-slate-500">
            <span>© {{ date('Y') }} {{ $company?->name ?? config('app.name') }}. All rights reserved.</span>
        </div>
    </div>

</footer>