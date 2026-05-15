<div class="space-y-5 rounded-[2rem] border border-[#004777]/10 bg-white/95 p-6 shadow-[0_20px_60px_-24px_rgba(0,71,119,0.25)] backdrop-blur">
    <div>
        <h1 class="text-2xl font-bold text-[#004777]">{{ __('auth.reset_password.title') }}</h1>
        <p class="text-sm text-[#004777]/70">{{ __('auth.reset_password.subtitle') }}</p>
    </div>

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input type="email" required autocomplete="email" wire:model.debounce.300ms="email"
                   placeholder="{{ __('auth.reset_password.email_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                   wire:model.debounce.300ms="password" placeholder="{{ __('auth.reset_password.password_placeholder') }}"
                   class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                   wire:model.debounce.300ms="password_confirmation"
                   placeholder="{{ __('auth.reset_password.confirm_password_placeholder') }}" class="w-full rounded-xl border border-[#004777]/15 bg-[#f4faff] px-4 py-2.5 text-[#004777] outline-none transition placeholder:text-[#004777]/35 focus:border-[#35A7FF] focus:bg-white">
            @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-xl bg-[#004777] py-2.5 text-white transition hover:bg-[#004777]/90">
            {{ __('auth.reset_password.submit') }}
        </button>
    </form>
</div>