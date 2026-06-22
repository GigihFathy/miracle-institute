<div class="space-y-6">
    <x-ui.page-header title="{{ __('admin.settings.page_title') }}" subtitle="{{ __('admin.settings.page_subtitle') }}" />

    <!-- General Settings Card (Yang sudah kamu buat) -->
    <div class="max-w-4xl space-y-4 rounded-2xl border bg-white p-6 shadow-sm">
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Profil Perusahaan</h3>
            <p class="text-sm text-gray-500">Atur informasi dasar dan kontak platform.</p>
        </div>

        <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
            <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.company_name') }} <span class="text-rose-500">*</span>
                </label>
                <input wire:model="name" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.company_name') }}">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.logo') }}
                </label>
                <input wire:model="logo" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.logo') }}">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">
                {{ __('admin.settings.form.description') }}
            </label>
            <textarea wire:model="description" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.description') }}"></textarea>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">
                {{ __('admin.settings.form.address') }}
            </label>
            <textarea wire:model="address" rows="2" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.address') }}"></textarea>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.vision') }}
                </label>
                <textarea wire:model="vision" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.vision') }}"></textarea>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.mission') }}
                </label>
                <textarea wire:model="mission" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.mission') }}"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.facebook') }}
                </label>
                <input wire:model="facebook" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.facebook') }}">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.instagram') }}
                </label>
                <input wire:model="instagram" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.instagram') }}">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.youtube') }}
                </label>
                <input wire:model="youtube" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.youtube') }}">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    {{ __('admin.settings.form.email') }}
                </label>
                <input wire:model="email" type="email" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.email') }}">
            </div>
        </div>

        <button wire:click="save" class="admin-primary-button rounded-xl border border-brand-dark/20 px-5 py-3 transition">
            {{ __('admin.settings.actions.save') }}
        </button>
    </div>

     <div class="max-w-4xl rounded-2xl border bg-white p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.71 3.5L1.15 15l3.43 6 6.55-11.5M9.73 15L6.3 21h13.12l3.43-6m-1.72-2.5L14.58 1H7.71l6.55 11.5" />
                    </svg>
                    {{ __('admin.settings.drive.title') }}
                </h3>
                <p class="text-sm text-gray-500 max-w-2xl">
                    {{ __('admin.settings.drive.description') }}
                </p>
            </div>
            
            <!-- Status Badge -->
            <div class="shrink-0">
                @if($isGoogleConnected)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('admin.settings.drive.status.connected') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ __('admin.settings.drive.status.disconnected') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-center gap-4 border-t pt-5">
            @if($isGoogleConnected)
                <button 
                    wire:click="openDisconnectGoogleModal"
                    class="rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:ring-4 focus:ring-red-100 transition-all w-full sm:w-auto"
                >
                    {{ __('admin.settings.actions.disconnect_google') }}
                </button>
                <p class="text-xs text-gray-500">
                    {{ __('admin.settings.drive.notes.active') }}
                </p>
            @else
                <button 
                    wire:click="connectGoogle" 
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all w-full sm:w-auto"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.344-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/>
                    </svg>
                    {{ __('admin.settings.actions.connect_google') }}
                </button>
                <p class="text-xs text-amber-600">
                    {{ __('admin.settings.drive.notes.warning') }}
                </p>
            @endif
        </div>

        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
            Callback OAuth Google yang harus didaftarkan:
            <span class="mt-1 block font-mono text-[11px] text-slate-800">{{ config('services.google.redirect') }}</span>
        </div>
    </div>

    @if($showDisconnectGoogleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <button type="button" class="absolute inset-0" wire:click="closeDisconnectGoogleModal" aria-label="Tutup modal"></button>

            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Putuskan Integrasi Cloud Storage (Google Drive)</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Untuk keamanan, masukkan password default sebelum memutus koneksi Google Drive.
                    </p>
                </div>

                <div class="mt-5">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Password default</label>
                    <input
                        type="password"
                        wire:model.defer="disconnectGooglePassword"
                        class="w-full rounded-xl border px-4 py-2.5"
                        placeholder="Masukkan password"
                    >
                    @error('disconnectGooglePassword') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeDisconnectGoogleModal"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="disconnectGoogle"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700"
                    >
                        Putuskan Koneksi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
