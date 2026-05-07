<div class="space-y-6">
    <x-ui.page-header
        title="User Role Manager"
        subtitle="{{ $user->full_name }} · {{ $user->email }}"
    />

    <div class="rounded-2xl bg-white border p-6 space-y-5 max-w-4xl">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm text-slate-500">Selected roles</div>
                <div class="text-2xl font-bold text-slate-900">{{ count($selectedRoles) }}</div>
            </div>

            <div class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                Toggle roles below
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($roles as $role)
                <label class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex items-start gap-3 hover:border-slate-300 transition">
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
                href="{{ route('admin.users.index') }}"
                class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50"
            >
                ← Back
            </a>

            <button
                wire:click="save"
                class="px-5 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800"
            >
                Save Roles
            </button>
        </div>
    </div>
</div>