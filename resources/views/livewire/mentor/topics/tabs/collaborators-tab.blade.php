<section class="rounded-2xl border bg-white p-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold">Collaborators</h2>
            <p class="text-sm text-slate-500">Mentor utama dan collaborator pada topic ini.</p>
        </div>

        <button type="button"
                wire:click="openCollaboratorModal"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700">
            Invite Collaborator
        </button>
    </div>

    <div class="mt-5 grid gap-3">
        <div class="rounded-2xl border p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="font-semibold text-slate-900">{{ $owner?->name }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ $owner?->email }}</div>
                </div>

                <span class="rounded-full border px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                    owner
                </span>
            </div>
        </div>

        @forelse($collaborators as $collaborator)
            <div class="rounded-2xl border p-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="font-semibold text-slate-900">{{ $collaborator->user?->name }}</div>
                            <span class="rounded-full border px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                                {{ $collaborator->role_type }}
                            </span>
                            <span class="rounded-full border px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                                {{ $collaborator->status }}
                            </span>
                        </div>
                        <div class="mt-1 text-xs text-slate-500">{{ $collaborator->user?->email }}</div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @forelse($collaborator->permissions as $permission)
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] text-slate-600">
                                {{ $permissionLabels[$permission->permission] ?? $permission->permission }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-500">No custom permissions</span>
                        @endforelse

                        <button type="button"
                                wire:click="editCollaborator('{{ $collaborator->id }}')"
                                class="rounded-xl border px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">
                            Edit
                        </button>

                        <button type="button"
                                wire:click="removeCollaborator('{{ $collaborator->id }}')"
                                class="rounded-xl border px-3 py-2 text-xs text-rose-600 hover:bg-rose-50">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed p-6 text-sm text-slate-500">
                Belum ada collaborator.
            </div>
        @endforelse
    </div>

    <x-ui.mentor.modal
        :show="$showCollaboratorModal"
        title="{{ $editingCollaboratorId ? 'Edit Collaborator' : 'Invite Collaborator' }}"
        subtitle="Hanya akun Mentor/Disciples yang dapat ditambahkan."
        wire:click="closeCollaboratorModal"
    >
        <form wire:submit.prevent="saveCollaborator" class="space-y-4">
            <div class="grid gap-4">
                @if(! $editingCollaboratorId)
                    <div>
                        <label class="text-xs font-medium text-slate-500">Search User</label>
                        <input wire:model.live="collaboratorSearch" class="mt-1 w-full rounded-xl border px-4 py-2" placeholder="Name or email">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-slate-500">Select User</label>
                        <select wire:model.defer="collaboratorUserId" class="mt-1 w-full rounded-xl border px-4 py-2">
                            <option value="">Pilih Mentor</option>
                            @foreach($eligibleUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</option>
                            @endforeach
                        </select>
                        @error('collaboratorUserId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

                        @if($eligibleUsers->isEmpty())
                            <p class="mt-2 text-sm text-slate-500">Tidak ada mentor yang cocok atau sudah terhubung ke topic ini.</p>
                        @endif
                    </div>
                @else
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">User</div>
                        <div class="mt-1 text-sm font-medium">{{ $collaborators->firstWhere('id', $editingCollaboratorId)?->user?->name }}</div>
                        <div class="text-xs text-slate-500">{{ $collaborators->firstWhere('id', $editingCollaboratorId)?->user?->email }}</div>
                    </div>
                @endif

                <div>
                    <div class="text-xs font-medium text-slate-500">Permissions</div>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($permissionLabels as $permission => $label)
                            <label class="flex items-center gap-2 rounded-xl border px-4 py-3 text-sm">
                                <input type="checkbox" wire:model.defer="collaboratorPermissions" value="{{ $permission }}">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('collaboratorPermissions') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('collaboratorPermissions.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Status</label>
                    <select wire:model.defer="collaboratorStatus" class="mt-1 w-full rounded-xl border px-4 py-2">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('collaboratorStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4">
                <button type="button" wire:click="closeCollaboratorModal" class="rounded-xl border px-4 py-2 text-sm text-slate-600">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    {{ $editingCollaboratorId ? 'Update Collaborator' : 'Save Collaborator' }}
                </button>
            </div>
        </form>
    </x-ui.mentor.modal>
</section>