@php
    $roleNames = collect($user?->roles ?? [])
        ->pluck('name')
        ->filter()
        ->values();

    $hasDualRoles = $roleNames->count() > 1;
    $roleSummaryText = $hasDualRoles
        ? 'Anda dapat menjadi ' . $roleNames->join(' dan ')
        : null;
@endphp

<div class="space-y-6 px-4 pb-6 pt-6 sm:px-6 lg:px-12 xl:px-36">
    <x-ui.page-header title="{{ __('general.profile.hero.title') }}" />

    <div class="rounded-[28px] border border-[#d7dcef] bg-white px-6 py-6 shadow-sm sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-lg font-semibold tracking-tight text-[#2c314b]">{{ __('general.profile.form.title') }}</p>
                <p class="mt-1 text-sm leading-6 text-[#5f6785]">{{ __('general.profile.form.subtitle') }}</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[420px] lg:flex-1">
                <div class="rounded-2xl border border-[#d7dcef] bg-[#f4faff] px-4 py-4">
                    <p class="text-xs font-medium tracking-wide text-[#5f6785]">{{ __('general.profile.stats.status') }}</p>
                    <p class="mt-2 text-sm font-semibold text-[#004777]">
                        {{ $user?->hasVerifiedEmail() ? __('general.profile.status.verified') : __('general.profile.status.unverified') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-[#d7dcef] bg-[#f4faff] px-4 py-4">
                    <p class="text-xs font-medium tracking-wide text-[#5f6785]">{{ __('general.profile.hero.active_role') }}</p>
                    <p class="mt-2 text-sm font-semibold text-[#004777]">{{ ucfirst((string) $activeRole) }}</p>
                    @if($hasDualRoles)
                        <p class="mt-2 text-xs leading-5 text-[#5f6785]">{{ $roleSummaryText }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-[28px] border border-[#d7dcef] bg-white px-6 py-6 shadow-sm sm:px-8">
            <h2 class="text-lg font-semibold text-[#2c314b]">{{ __('general.profile.summary.title') }}</h2>

            <dl class="mt-5 space-y-4">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.fields.name') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">{{ $user?->name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.fields.email') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">{{ $user?->email }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.fields.phone') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">{{ $user?->phone ?: __('general.profile.summary.empty') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.fields.gender') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">
                        @if($user?->gender === 'male')
                            {{ __('general.profile.gender.male') }}
                        @elseif($user?->gender === 'female')
                            {{ __('general.profile.gender.female') }}
                        @else
                            {{ __('general.profile.summary.empty') }}
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.fields.dob') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">
                        {{ $user?->dob ? $user->dob->translatedFormat('d M Y') : __('general.profile.summary.empty') }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sm text-[#5f6785]">{{ __('general.profile.hero.member_since') }}</dt>
                    <dd class="text-sm font-medium text-[#2c314b]">{{ optional($user?->created_at)->translatedFormat('d M Y') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-[28px] border border-[#d7dcef] bg-white px-6 py-6 shadow-sm sm:px-8">
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[#2c314b]">{{ __('general.profile.fields.name') }}</label>
                    <input
                        wire:model.blur="name"
                        type="text"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#004777] focus:bg-white"
                    >
                    @error('name') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-[#2c314b]">{{ __('general.profile.fields.email') }}</label>
                    <input
                        wire:model.blur="email"
                        type="email"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#004777] focus:bg-white"
                    >
                    @error('email') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[#2c314b]">{{ __('general.profile.fields.phone') }}</label>
                        <input
                            wire:model.blur="phone"
                            type="text"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#004777] focus:bg-white"
                        >
                        @error('phone') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[#2c314b]">{{ __('general.profile.fields.gender') }}</label>
                        <select
                            wire:model.live="gender"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#004777] focus:bg-white"
                        >
                            <option value="">{{ __('general.profile.form.select_gender') }}</option>
                            <option value="male">{{ __('general.profile.gender.male') }}</option>
                            <option value="female">{{ __('general.profile.gender.female') }}</option>
                        </select>
                        @error('gender') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-[#2c314b]">{{ __('general.profile.fields.dob') }}</label>
                    <input
                        wire:model.live="dob"
                        type="date"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#004777] focus:bg-white"
                    >
                    @error('dob') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-[#004777] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#003a5f]"
                    >
                        {{ __('general.profile.form.save') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
