<div class="space-y-6">
    <x-ui.page-header
        title="User Role Manager"
        subtitle="{{ $user->full_name }} · {{ $user->email }}"
    />

    <div class="rounded-2xl bg-white border p-6 space-y-5 max-w-3xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($roles as $role)
                <label class="rounded-2xl border p-4 flex items-center gap-3">
                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}">
                    <div>
                        <div class="font-medium">{{ $role->name }}</div>
                        <div class="text-xs text-slate-500">{{ $role->label }}</div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="flex items-center justify-between">
            <a
                href="{{ route('admin.users.index') }}"
                class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50"
            >
                ← Back
            </a>

            <button
                wire:click="save"
                class="px-5 py-3 rounded-xl bg-slate-900 text-white"
            >
                Save Roles
            </button>
        </div>
    </div>
</div>