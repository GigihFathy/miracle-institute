<div class="space-y-5 rounded-[2rem] border border-[#004777]/10 bg-white/95 p-6 shadow-[0_20px_60px_-24px_rgba(0,71,119,0.25)] backdrop-blur">
    <div>
        <h1 class="text-2xl font-bold text-[#004777]">{{ __('auth.forgot_password.title') }}</h1>
        <p class="text-sm text-[#004777]/70">{{ __('auth.forgot_password.subtitle') }}</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl bg-[#35A7FF]/10 px-4 py-3 text-sm text-[#004777]">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input type="email" required autocomplete="email" wire:model.debounce.300ms="email"
                   placeholder="{{ __('auth.forgot_password.email_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-xl bg-[#004777] py-2.5 text-white transition hover:bg-[#004777]/90">
            {{ __('auth.forgot_password.submit') }}
        </button>
    </form>

    <div class="text-sm text-center">
        <a href="{{ localized_route('login') }}" class="text-[#35A7FF] underline decoration-[#35A7FF]/50 underline-offset-4">
            {{ __('auth.forgot_password.back_to_login') }}
        </a>
    </div>
</div>