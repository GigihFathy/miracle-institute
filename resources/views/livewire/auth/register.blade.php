<div class="space-y-5 rounded-[2rem] border border-[#004777]/10 bg-white/95 p-6 shadow-[0_20px_60px_-24px_rgba(0,71,119,0.25)] backdrop-blur">
    <div>
        <h1 class="text-2xl font-bold text-[#004777]">{{ __('auth.register.title') }}</h1>
        <p class="text-sm text-[#004777]/70">{{ __('auth.register.subtitle') }}</p>
    </div>

    @if (session('warning'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('warning') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <div class="mb-1 flex items-center justify-between gap-3">
                <span class="text-sm font-medium text-[#004777]">{{ __('general.profile.fields.name') }}</span>
                <span class="text-[11px] text-[#004777]/45">{{ mb_strlen($name ?? '') }}/35</span>
            </div>
            <input type="text" required minlength="2" maxlength="35" wire:model.live.debounce.300ms="name"
                placeholder="{{ __('auth.register.name_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        
        <div>
            <input type="email" required autocomplete="email" wire:model.debounce.300ms="email"
                     placeholder="{{ __('auth.register.email_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
                 @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                     wire:model.debounce.300ms="password" placeholder="{{ __('auth.register.password_placeholder') }}"
                     class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
                 @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                   wire:model.debounce.300ms="password_confirmation"
                   placeholder="{{ __('auth.register.confirm_password_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
            @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submit"
            class="w-full rounded-xl bg-[#004777] py-2.5 text-white transition hover:bg-[#004777]/90 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="submit">
                {{ __('auth.register.submit') }}
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

    <div class="text-sm text-center">
        <a href="{{ localized_route('login') }}" class="text-[#35A7FF] underline decoration-[#35A7FF]/50 underline-offset-4">
            {{ __('auth.register.already_have_account') }}
        </a>
    </div>
</div>
