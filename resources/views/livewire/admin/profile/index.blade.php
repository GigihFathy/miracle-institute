<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.profile.page_title') }}"
        subtitle="{{ __('admin.profile.page_subtitle') }}"
    />

    <div class="max-w-2xl space-y-4">
        {{-- Tabs --}}
        <div class="flex gap-1 rounded-xl border bg-white p-1">
            <button wire:click="setActiveTab('account')"
                    class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === 'account' ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                {{ __('admin.profile.tabs.account') }}
            </button>
            <button wire:click="setActiveTab('password')"
                    class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === 'password' ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                {{ __('admin.profile.tabs.password') }}
            </button>
        </div>

        {{-- Account Tab --}}
        @if($activeTab === 'account')
            <div class="space-y-4 rounded-2xl border bg-white p-6">
                <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                    <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                </div>

                @if($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Periksa kembali field yang wajib diisi.
                    </div>
                @endif

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.name') }} <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="name"
                           class="w-full rounded-xl border px-4 py-2 @error('name') border-rose-400 @enderror"
                           placeholder="{{ __('admin.profile.form.name') }}">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.email') }} <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="email" type="email"
                           class="w-full rounded-xl border px-4 py-2 @error('email') border-rose-400 @enderror"
                           placeholder="{{ __('admin.profile.form.email') }}">
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.phone') }}
                    </label>
                    <input wire:model="phone"
                           class="w-full rounded-xl border px-4 py-2 @error('phone') border-rose-400 @enderror"
                           placeholder="628xxxxxxxxxx">
                    @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            {{ __('admin.profile.form.gender') }}
                        </label>
                        <select wire:model="gender"
                                class="w-full rounded-xl border px-4 py-2 @error('gender') border-rose-400 @enderror">
                            <option value="">— Pilih —</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                        @error('gender') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            {{ __('admin.profile.form.dob') }}
                        </label>
                        <input wire:model="dob" type="date"
                               class="w-full rounded-xl border px-4 py-2 @error('dob') border-rose-400 @enderror">
                        @error('dob') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="admin-primary-button inline-flex min-w-[9rem] items-center justify-center gap-2 rounded-xl border border-brand-dark/20 px-5 py-2.5 text-sm transition disabled:cursor-not-allowed disabled:opacity-70">
                        <span wire:loading.remove wire:target="save">{{ __('admin.profile.actions.save') }}</span>
                        <span wire:loading.inline-flex wire:target="save" class="items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Password Tab --}}
        @if($activeTab === 'password')
            <div class="space-y-4 rounded-2xl border bg-white p-6">
                @if($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Periksa kembali field di bawah.
                    </div>
                @endif

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.current_password') }} <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="currentPassword" type="password"
                           class="w-full rounded-xl border px-4 py-2 @error('currentPassword') border-rose-400 @enderror"
                           autocomplete="current-password">
                    @error('currentPassword') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.new_password') }} <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="password" type="password"
                           class="w-full rounded-xl border px-4 py-2 @error('password') border-rose-400 @enderror"
                           autocomplete="new-password">
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        {{ __('admin.profile.form.confirm_password') }} <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="password_confirmation" type="password"
                           class="w-full rounded-xl border px-4 py-2"
                           autocomplete="new-password">
                </div>

                <div class="rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
                    Setelah kata sandi diubah, Anda akan otomatis keluar dan perlu login kembali.
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="updatePassword"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            class="admin-primary-button inline-flex min-w-[9rem] items-center justify-center gap-2 rounded-xl border border-brand-dark/20 px-5 py-2.5 text-sm transition disabled:cursor-not-allowed disabled:opacity-70">
                        <span wire:loading.remove wire:target="updatePassword">{{ __('admin.profile.actions.update_password') }}</span>
                        <span wire:loading.inline-flex wire:target="updatePassword" class="items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
