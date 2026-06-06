@php
    $roleNames = collect($user?->roles ?? [])
        ->pluck('name')
        ->filter()
        ->values();

    $initials = collect(explode(' ', trim((string) $user?->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');

    $isVerified = filled($user?->email_verified_at);
@endphp

<div class="min-h-screen bg-white px-4 pb-16 pt-8 text-[#0f172a] sm:px-6 sm:pb-24 sm:pt-12 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-10">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-[2rem] border border-[#004777]/15 bg-white px-7 py-10 sm:px-10 sm:py-14 lg:px-14">
            <div class="flex flex-col items-center gap-6 text-center sm:flex-row sm:text-left">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#004777]/60">
                        {{ __('general.profile.hero.badge') }}
                    </p>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                        {{ __('general.profile.hero.subtitle') }}
                    </p>

                    <div class="mt-5 flex flex-wrap justify-center gap-2 sm:justify-start">
                        @forelse($roleNames as $role)
                            <span class="rounded-full bg-[#eef8ff] px-3 py-1.5 text-xs font-semibold capitalize text-[#004777]">
                                {{ $role }}
                            </span>
                        @empty
                            <span class="rounded-full bg-[#eef8ff] px-3 py-1.5 text-xs font-semibold text-[#004777]">
                                {{ __('general.profile.summary.empty') }}
                            </span>
                        @endforelse

                        <span @class([
                            'rounded-full px-3 py-1.5 text-xs font-semibold',
                            'bg-[#004777] text-white' => $isVerified,
                            'bg-slate-100 text-slate-600' => !$isVerified,
                        ])>
                            {{ $isVerified ? __('general.profile.status.verified') : __('general.profile.status.unverified') }}
                        </span>
                    </div>

                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="rounded-[2rem] bg-slate-50 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-[#004777]">{{ __('general.profile.summary.title') }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('general.profile.form.subtitle') }}</p>

                <dl class="mt-7 space-y-3">
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.fields.name') }}</dt>
                        <dd class="mt-1 break-words text-sm font-bold text-[#004777]">{{ $user?->name }}</dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.fields.email') }}</dt>
                        <dd class="mt-1 break-words text-sm font-bold text-[#004777]">{{ $user?->email }}</dd>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.fields.phone') }}</dt>
                            <dd class="mt-1 text-sm font-bold text-[#004777]">{{ $user?->phone ?: __('general.profile.summary.empty') }}</dd>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.fields.gender') }}</dt>
                            <dd class="mt-1 text-sm font-bold text-[#004777]">
                                @if($user?->gender === 'male')
                                    {{ __('general.profile.gender.male') }}
                                @elseif($user?->gender === 'female')
                                    {{ __('general.profile.gender.female') }}
                                @else
                                    {{ __('general.profile.summary.empty') }}
                                @endif
                            </dd>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.fields.dob') }}</dt>
                        <dd class="mt-1 text-sm font-bold text-[#004777]">
                            {{ $user?->dob ? $user->dob->translatedFormat('d M Y') : __('general.profile.summary.empty') }}
                        </dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('general.profile.hero.member_since') }}</dt>
                        <dd class="mt-1 text-sm font-bold text-[#004777]">{{ optional($user?->created_at)->translatedFormat('d M Y') }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 sm:p-8">
                <div class="flex gap-2 rounded-xl bg-slate-100 p-1">
                    <button type="button"
                            wire:click="setActiveTab('account')"
                            @class([
                                'flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition',
                                'bg-white text-[#004777]' => $activeTab === 'account',
                                'text-slate-500 hover:text-[#004777]' => $activeTab !== 'account',
                            ])>
                        {{ __('general.profile.form.account_tab') }}
                    </button>
                    <button type="button"
                            wire:click="setActiveTab('password')"
                            @class([
                                'flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition',
                                'bg-white text-[#004777]' => $activeTab === 'password',
                                'text-slate-500 hover:text-[#004777]' => $activeTab !== 'password',
                            ])>
                        {{ __('general.profile.form.password_tab') }}
                    </button>
                </div>

                @if($activeTab === 'account')
                    <div class="mt-7">
                        <h2 class="text-2xl font-bold text-[#004777]">{{ __('general.profile.form.account_tab') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('general.profile.form.subtitle') }}</p>
                    </div>

                    <form wire:submit="save" novalidate class="mt-7 space-y-5">
                        @if($errors->any())
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                                {{ __('general.profile.form.validation_title') }}
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.name') }}</label>
                            <input wire:model.live.debounce.300ms="name" type="text"
                                   aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                   aria-describedby="name-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('name'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('name'),
                                   ])>
                            @error('name') <span id="name-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.email') }}</label>
                            <input wire:model.live.debounce.300ms="email" type="email"
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   aria-describedby="email-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('email'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('email'),
                                   ])>
                            @error('email') <span id="email-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.phone') }}</label>
                                <input wire:model.live.debounce.300ms="phone" type="text"
                                       inputmode="numeric"
                                       placeholder="62012345678"
                                       aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                       aria-describedby="phone-error"
                                       @class([
                                           'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                           'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('phone'),
                                           'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('phone'),
                                       ])>
                                @error('phone') <span id="phone-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.gender') }}</label>
                                <select wire:model.live="gender"
                                        aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}"
                                        aria-describedby="gender-error"
                                        @class([
                                            'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                            'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('gender'),
                                            'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('gender'),
                                        ])>
                                    <option value="">{{ __('general.profile.form.select_gender') }}</option>
                                    <option value="male">{{ __('general.profile.gender.male') }}</option>
                                    <option value="female">{{ __('general.profile.gender.female') }}</option>
                                </select>
                                @error('gender') <span id="gender-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.dob') }}</label>
                            <input wire:model.live="dob" type="date"
                                   aria-invalid="{{ $errors->has('dob') ? 'true' : 'false' }}"
                                   aria-describedby="dob-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('dob'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('dob'),
                                   ])>
                            @error('dob') <span id="dob-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="save"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f] disabled:opacity-70">
                                <span wire:loading.remove wire:target="save">{{ __('general.profile.form.save') }}</span>
                                <span wire:loading wire:target="save">{{ __('general.profile.form.save') }}...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-7">
                        <h2 class="text-2xl font-bold text-[#004777]">{{ __('general.profile.form.password_title') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('general.profile.form.password_subtitle') }}</p>
                    </div>

                    <div class="mt-7 space-y-5">
                        @if($errors->hasAny(['currentPassword', 'password', 'password_confirmation']))
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                                {{ __('general.profile.form.validation_title') }}
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.current_password') }}</label>
                            <input wire:model.live.debounce.300ms="currentPassword" type="password" autocomplete="current-password"
                                   aria-invalid="{{ $errors->has('currentPassword') ? 'true' : 'false' }}"
                                   aria-describedby="current-password-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('currentPassword'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('currentPassword'),
                                   ])>
                            @error('currentPassword') <span id="current-password-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.new_password') }}</label>
                            <input wire:model.live.debounce.300ms="password" type="password" autocomplete="new-password"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   aria-describedby="password-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('password'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('password'),
                                   ])>
                            <p class="text-xs text-slate-500">{{ __('general.profile.form.password_help') }}</p>
                            @error('password') <span id="password-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#004777]">{{ __('general.profile.fields.password_confirmation') }}</label>
                            <input wire:model.live.debounce.300ms="password_confirmation" type="password" autocomplete="new-password"
                                   aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                                   aria-describedby="password-confirmation-error"
                                   @class([
                                       'w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:bg-white focus:ring-4',
                                       'border-slate-200 focus:border-[#35A7FF] focus:ring-[#35A7FF]/10' => !$errors->has('password_confirmation'),
                                       'border-rose-400 focus:border-rose-500 focus:ring-rose-100' => $errors->has('password_confirmation'),
                                   ])>
                            @error('password_confirmation') <span id="password-confirmation-error" class="text-xs font-medium text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="button"
                                    wire:click="updatePassword"
                                    wire:loading.attr="disabled"
                                    wire:target="updatePassword"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f] disabled:opacity-70">
                                <span wire:loading.remove wire:target="updatePassword">{{ __('general.profile.form.update_password') }}</span>
                                <span wire:loading wire:target="updatePassword">{{ __('general.profile.form.update_password') }}...</span>
                            </button>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
