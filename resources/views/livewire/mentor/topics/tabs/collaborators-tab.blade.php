<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)]">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-[var(--mentor-primary)]">{{ __('mentor.topic_tabs.collaborators.title') }}</h2>
            <p class="text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ __('mentor.topic_tabs.collaborators.subtitle') }}</p>
        </div>

        <button type="button"
                wire:click="openCollaboratorModal"
                class="admin-primary-button rounded-xl px-4 py-2 text-sm">
            {{ __('mentor.topic_tabs.collaborators.actions.invite') }}
        </button>
    </div>

    <div class="mt-5 grid gap-3">
        <div class="rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="font-semibold text-[var(--mentor-primary)]">{{ $owner?->name }}</div>
                    <div class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ $owner?->email }}</div>
                </div>

                <span class="rounded-full border border-slate-200 bg-white px-2 py-1 text-[11px] uppercase tracking-wide text-[var(--mentor-primary)]">
                    {{ __('mentor.topic_tabs.collaborators.owner') }}
                </span>
            </div>
        </div>

        @forelse($collaborators as $collaborator)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="font-semibold text-[var(--mentor-primary)]">{{ $collaborator->user?->name }}</div>
                            <span class="rounded-full border border-slate-200 px-2 py-1 text-[11px] uppercase tracking-wide text-[var(--mentor-primary)]">
                                {{ $collaborator->role_type }}
                            </span>
                            <span class="rounded-full border border-slate-200 px-2 py-1 text-[11px] uppercase tracking-wide text-[var(--mentor-primary)]">
                                {{ $collaborator->status }}
                            </span>
                        </div>
                        <div class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ $collaborator->user?->email }}</div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @forelse($collaborator->permissions as $permission)
                            <span class="rounded-full border border-slate-200 bg-[var(--mentor-primary-soft-2)] px-2 py-1 text-[11px] text-[var(--mentor-primary)]">
                                {{ $permissionLabels[$permission->permission] ?? $permission->permission }}
                            </span>
                        @empty
                            <span class="text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ __('mentor.topic_tabs.collaborators.no_custom_permissions') }}</span>
                        @endforelse

                        <button type="button"
                                wire:click="editCollaborator('{{ $collaborator->id }}')"
                                class="admin-edit-button rounded-xl px-3 py-2 text-xs">
                            {{ __('mentor.topic_tabs.collaborators.actions.edit') }}
                        </button>

                        <button type="button"
                                wire:click="removeCollaborator('{{ $collaborator->id }}')"
                                class="admin-delete-button rounded-xl px-3 py-2 text-xs">
                            {{ __('mentor.topic_tabs.collaborators.actions.remove') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-6 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                {{ __('mentor.topic_tabs.collaborators.empty') }}
            </div>
        @endforelse
    </div>

    <x-ui.mentor.modal
        :show="$showCollaboratorModal"
        title="{{ $editingCollaboratorId ? __('mentor.topic_tabs.collaborators.modal.edit_title') : __('mentor.topic_tabs.collaborators.modal.add_title') }}"
        subtitle="{{ __('mentor.topic_tabs.collaborators.modal.subtitle') }}"
        wire:click="closeCollaboratorModal"
    >
        <form wire:submit.prevent="saveCollaborator" class="space-y-4">
            <div class="grid gap-4">
                @if(! $editingCollaboratorId)
                    <div>
                        <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.collaborators.form.search_user') }}</label>
                        <input wire:model.live="collaboratorSearch" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2" placeholder="{{ __('mentor.topic_tabs.collaborators.form.search_placeholder') }}">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.collaborators.form.select_user') }}</label>
                        <select wire:model.defer="collaboratorUserId" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                            <option value="">{{ __('mentor.topic_tabs.collaborators.form.select_placeholder') }}</option>
                            @foreach($eligibleUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</option>
                            @endforeach
                        </select>
                        @error('collaboratorUserId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

                        @if($eligibleUsers->isEmpty())
                            <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ __('mentor.topic_tabs.collaborators.form.no_eligible_users') }}</p>
                        @endif
                    </div>
                @else
                    <div class="rounded-xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                        <div class="text-xs text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.collaborators.form.user') }}</div>
                        <div class="mt-1 text-sm font-medium text-[var(--mentor-primary)]">{{ $collaborators->firstWhere('id', $editingCollaboratorId)?->user?->name }}</div>
                        <div class="text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ $collaborators->firstWhere('id', $editingCollaboratorId)?->user?->email }}</div>
                    </div>
                @endif

                <div>
                    <div class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.collaborators.form.permissions') }}</div>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($permissionLabels as $permission => $label)
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-[var(--mentor-primary)]">
                                <input type="checkbox" wire:model.defer="collaboratorPermissions" value="{{ $permission }}">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('collaboratorPermissions') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('collaboratorPermissions.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.collaborators.form.status') }}</label>
                    <select wire:model.defer="collaboratorStatus" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                        <option value="active">{{ __('mentor.topic_tabs.collaborators.form.status_active') }}</option>
                        <option value="inactive">{{ __('mentor.topic_tabs.collaborators.form.status_inactive') }}</option>
                    </select>
                    @error('collaboratorStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                <button type="button" wire:click="closeCollaboratorModal" class="admin-neutral-button rounded-xl px-4 py-2 text-sm">
                    {{ __('mentor.topic_tabs.collaborators.form.cancel') }}
                </button>
                <button type="submit" class="admin-primary-button rounded-xl px-4 py-2 text-sm">
                    {{ $editingCollaboratorId ? __('mentor.topic_tabs.collaborators.form.update') : __('mentor.topic_tabs.collaborators.form.save') }}
                </button>
            </div>
        </form>
    </x-ui.mentor.modal>
</section>
