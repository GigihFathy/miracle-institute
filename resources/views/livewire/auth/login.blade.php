<div class="space-y-5 rounded-[2rem] border border-[#004777]/10 bg-white/95 p-6 shadow-[0_20px_60px_-24px_rgba(0,71,119,0.25)] backdrop-blur">
    <div class="flex justify-center">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Miracle Institute"
            class="h-20 w-auto object-contain"
        >
    </div>

    <div>
        <h1 class="text-2xl font-bold text-[#004777]">{{ __('auth.login.title') }}</h1>
        <p class="text-sm text-[#004777]/70">{{ __('auth.login.subtitle') }}</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl bg-[#35A7FF]/10 px-4 py-3 text-sm text-[#004777]">
            {{ session('status') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('warning') }}
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

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submit"
            class="w-full rounded-xl bg-[#004777] py-2.5 text-white transition hover:bg-[#004777]/90 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="submit">
                {{ __('auth.login.submit') }}
            </span>
            <span wire:loading.inline-flex wire:target="submit" class="items-center justify-center gap-2">
                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Loading...</span>
            </span>
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
