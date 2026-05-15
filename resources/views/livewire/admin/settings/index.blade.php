<div class="space-y-6">
    <x-ui.page-header title="{{ __('admin.settings.page_title') }}" subtitle="{{ __('admin.settings.page_subtitle') }}" />

    <div class="max-w-4xl space-y-4 rounded-2xl border bg-white p-6">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input wire:model="name" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.company_name') }}">
            <input wire:model="logo" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.logo') }}">
        </div>

        <textarea wire:model="description" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.description') }}"></textarea>
        <textarea wire:model="address" rows="2" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.address') }}"></textarea>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <textarea wire:model="vision" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.vision') }}"></textarea>
            <textarea wire:model="mission" rows="3" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.mission') }}"></textarea>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input wire:model="facebook" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.facebook') }}">
            <input wire:model="instagram" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.instagram') }}">
            <input wire:model="youtube" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.youtube') }}">
            <input wire:model="whatsapp" class="rounded-xl border px-4 py-2" placeholder="{{ __('admin.settings.form.whatsapp') }}">
        </div>

        <button wire:click="save" class="rounded-xl bg-slate-900 px-5 py-3 text-white">
            {{ __('admin.settings.actions.save') }}
        </button>
    </div>
</div>