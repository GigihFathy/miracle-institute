<section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <div class="mentor-workspace-panel flex h-full w-full flex-col">
        <div class="mb-6 flex flex-col items-center justify-between gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end sm:text-left">
            <div class="text-center sm:text-left">
                <h2 class="mentor-workspace-heading">
                    {{ __('mentor.topic_tabs.materials.selected.title') }}
                </h2>
                <p class="mentor-workspace-subheading">
                    {{ __('mentor.topic_tabs.materials.selected.subtitle') }}
                </p>
            </div>

            <div class="flex w-full flex-wrap justify-center gap-3 sm:w-auto sm:justify-end">
                @if($selectedMaterial)
                    <button type="button"
                            wire:click="editMaterial('{{ $selectedMaterial->id }}')"
                            class="admin-edit-button inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm shadow-sm transition-all duration-200 hover:shadow active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                            <path d="m15 5 4 4"/>
                        </svg>
                        {{ __('mentor.topic_tabs.materials.actions.edit') }}
                    </button>

                    <button type="button"
                            wire:click="deleteMaterial('{{ $selectedMaterial->id }}')"
                            class="admin-delete-button inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm shadow-sm transition-all duration-200 hover:shadow active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" x2="10" y1="11" y2="17"/>
                            <line x1="14" x2="14" y1="11" y2="17"/>
                        </svg>
                        {{ __('mentor.topic_tabs.materials.actions.delete') }}
                    </button>
                @endif
            </div>
        </div>

        <div class="flex w-full flex-grow flex-col">
            @if($selectedMaterial)
                @if($selectedMaterial->type === 'video' && $selectedMaterial->external_url)
                    <div class="mx-auto flex w-full max-w-3xl flex-col space-y-5">
                        <div class="group relative aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary)] shadow-md">
                            @if($videoThumbnailUrl)
                                <img src="{{ $videoThumbnailUrl }}" class="h-full w-full object-cover opacity-80" alt="{{ $selectedMaterial->name }}">
                            @endif
                            @if($videoEmbedUrl)
                                <a href="{{ $selectedMaterial->external_url }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 flex items-center justify-center">
                                    <div class="grid h-16 w-16 place-items-center rounded-full bg-white/90 text-[var(--mentor-primary)] shadow-xl">
                                        ▶
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                @elseif($materialPreviewUrl)
                    <div class="mx-auto flex w-full max-w-4xl flex-col space-y-5">
                        <div class="aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] shadow-sm">
                            <iframe src="{{ $materialPreviewUrl }}" class="h-full w-full" allowfullscreen></iframe>
                        </div>
                    </div>
                @else
                    <div class="mentor-workspace-empty min-h-[250px]">
                        No preview
                    </div>
                @endif
            @else
                <div class="mentor-workspace-empty min-h-[300px] flex-grow">
                    Empty
                </div>
            @endif
        </div>
    </div>

    <aside class="mentor-workspace-panel">
        <div class="flex items-center justify-between gap-3">
            <h2 class="mentor-workspace-heading">Materials</h2>

            @if($canAddMaterial)
                <button type="button"
                        wire:click="openMaterialModal"
                        class="admin-primary-button rounded-xl px-4 py-2 text-sm">
                    {{ __('mentor.topic_tabs.materials.list.actions.add') }}
                </button>
            @endif
        </div>

        <div class="mt-4 space-y-2">
            @forelse($materials as $material)
                <button type="button"
                        wire:key="material-{{ $material->id }}"
                        wire:click="selectMaterial(@js($material->id))"
                        class="w-full rounded-xl border p-4 text-left transition {{ $selectedMaterial?->id === $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-[var(--mentor-primary-soft-2)] text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }}">
                    <div class="truncate text-sm font-medium">
                        #{{ $material->sort_order }} · {{ $material->name }}
                    </div>
                    <div class="mt-1 text-xs">
                        {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}
                    </div>
                </button>
            @empty
                <div class="mentor-workspace-empty min-h-0">
                    No materials
                </div>
            @endforelse
        </div>
    </aside>

    @if($showMaterialModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6" wire:click.self="closeMaterialModal">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl" wire:keydown.escape="closeMaterialModal">
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--mentor-primary)]">
                            {{ $editingMaterialId ? 'Edit Material' : 'Add Material' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">Fill the form below.</p>
                    </div>

                    <button type="button" wire:click="closeMaterialModal" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveMaterial" enctype="multipart/form-data" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <label class="text-xs font-medium">Name</label>
                                <span class="text-[11px] text-slate-400">{{ mb_strlen($materialName ?? '') }}/50</span>
                            </div>
                            <input wire:model="materialName" maxlength="50" class="mentor-workspace-field mt-1">
                        </div>

                        <div>
                            <label class="text-xs font-medium">Type</label>
                            <select wire:model.live="materialType" class="mentor-workspace-field mt-1">
                                @foreach($materialTypeOptions as $type)
                                    <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium">Status</label>
                            <select wire:model="materialStatus" class="mentor-workspace-field mt-1">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        @if(in_array($materialType, ['pdf', 'ppt'], true))
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium">File</label>
                                <input type="file" wire:model="materialFile" accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" class="mentor-workspace-field mt-1">
                            </div>
                        @endif

                        @if($materialType === 'video')
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium">Video URL</label>
                                <input wire:model.live.debounce.500ms="materialExternalUrl" class="mentor-workspace-field mt-1">
                            </div>
                        @endif

                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium">Sort Order</label>
                            <input wire:model="materialSortOrder" type="number" min="0" class="mentor-workspace-field mt-1">
                        </div>
                    </div>

                    @error('materialName') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialType') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialFile') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialStatus') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialExternalUrl') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialSortOrder') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" wire:click="closeMaterialModal"
                                class="admin-neutral-button rounded-xl px-4 py-2 text-sm">
                            {{ __('mentor.topic_tabs.materials.form.cancel') }}
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="admin-primary-button rounded-xl px-4 py-2 text-sm">
                            {{ $editingMaterialId ? __('mentor.topic_tabs.materials.form.update') : __('mentor.topic_tabs.materials.form.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>
