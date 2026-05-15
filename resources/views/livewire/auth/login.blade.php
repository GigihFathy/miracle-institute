<div class="space-y-5 rounded-[2rem] border border-[#004777]/10 bg-white/95 p-6 shadow-[0_20px_60px_-24px_rgba(0,71,119,0.25)] backdrop-blur">
    <div>
        <h1 class="text-2xl font-bold text-[#004777]">{{ __('auth.login.title') }}</h1>
        <p class="text-sm text-[#004777]/70">{{ __('auth.login.subtitle') }}</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl bg-[#35A7FF]/10 px-4 py-3 text-sm text-[#004777]">
            {{ session('status') }}
        </div>
    @endif

    @error('oauth')
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input
                type="email"
                required
                autocomplete="email"
                wire:model.debounce.300ms="email"
                placeholder="{{ __('auth.login.email_placeholder') }}"
                class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white"
            >
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input
                type="password"
                required
                minlength="8"
                autocomplete="current-password"
                wire:model.debounce.300ms="password"
                placeholder="{{ __('auth.login.password_placeholder') }}"
                class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white"
            >
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-[#004777]/80">
            <input type="checkbox" class="rounded border-[#004777]/20 text-[#35A7FF] focus:ring-[#35A7FF]" wire:model="remember">
            {{ __('auth.login.remember_me') }}
        </label>

        <button type="submit" class="w-full rounded-xl bg-[#004777] py-2.5 text-white transition hover:bg-[#004777]/90">
            {{ __('auth.login.submit') }}
        </button>
    </form>

    <div class="space-y-3">
        <div class="flex items-center justify-between text-sm">
            <a href="{{ localized_route('password.request') }}" class="text-[#35A7FF] underline decoration-[#35A7FF]/50 underline-offset-4">
                {{ __('auth.login.forgot_password') }}
            </a>
            <a href="{{ localized_route('register') }}" class="text-[#35A7FF] underline decoration-[#35A7FF]/50 underline-offset-4">
                {{ __('auth.login.create_account') }}
            </a>
        </div>
    </div>
</div>