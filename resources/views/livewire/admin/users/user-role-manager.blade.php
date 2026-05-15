<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.user_role_manager.page_title') }}"
        subtitle="{{ $user->full_name }} · {{ $user->email }}"
    />

    <div class="max-w-4xl space-y-5 rounded-2xl border bg-white p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm text-slate-500">{{ __('admin.user_role_manager.selected_roles') }}</div>
                <div class="text-2xl font-bold text-slate-900">{{ count($selectedRoles) }}</div>
            </div>

            <div class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                {{ __('admin.user_role_manager.toggle_hint') }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($roles as $role)
                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300">
                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $role->name }}</div>
                        <div class="text-xs text-slate-500">{{ $role->label }}</div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="flex items-center justify-between">
            <a
                href="{{ localized_route('admin.users.index') }}"
                class="rounded-xl border px-4 py-2 text-sm hover:bg-slate-50"
            >
                {{ __('admin.user_role_manager.back') }}
            </a>

            <button
                wire:click="save"
                class="rounded-xl bg-slate-900 px-5 py-3 text-white hover:bg-slate-800"
            >
                {{ __('admin.user_role_manager.save') }}
            </button>
        </div>
    </div>
</div>