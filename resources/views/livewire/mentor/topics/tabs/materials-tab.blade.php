<section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <div class="flex h-full w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)] md:p-6">
        <div class="mb-6 flex flex-col items-center justify-between gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end sm:text-left">
            <div class="text-center sm:text-left">
                <h2 class="text-lg font-bold text-[var(--mentor-primary)] sm:text-xl">
                    {{ __('mentor.topic_tabs.materials.selected.title') }}
                </h2>
                <p class="mt-1.5 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                    {{ __('mentor.topic_tabs.materials.selected.subtitle') }}
                </p>
            </div>

            <div class="flex w-full flex-wrap justify-center gap-3 sm:w-auto sm:justify-end">
                @if($selectedMaterial)
                    <button type="button"
                            wire:key="edit-{{ $selectedMaterial->id }}"
                            wire:click="editMaterial(@js($selectedMaterial->id))"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-[var(--mentor-primary-soft-2)] px-4 py-2 text-sm font-medium text-[var(--mentor-primary)] shadow-sm transition-all duration-200 hover:bg-[var(--mentor-secondary)] hover:shadow active:scale-95">
                        Edit
                    </button>

                    <button type="button"
                            wire:key="delete-{{ $selectedMaterial->id }}"
                            onclick="if(!confirm('Yakin ingin menghapus material ini?')) return false;"
                            wire:click="deleteMaterial(@js($selectedMaterial->id))"
                            class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-white px-4 py-2 text-sm font-medium text-rose-600 shadow-sm transition-all duration-200 hover:bg-rose-50 hover:text-rose-700 hover:shadow active:scale-95">
                        Delete
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
                    <div class="flex min-h-[250px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-6 text-center">
                        No preview
                    </div>
                @endif
            @else
                <div class="flex min-h-[300px] flex-grow flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-6 text-center">
                    Empty
                </div>
            @endif
        </div>
    </div>

    <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)]">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-[var(--mentor-primary)]">Materials</h2>

            @if($canAddMaterial && $materials->count() < 3)
                <button type="button" wire:click="openMaterialModal" class="rounded-xl bg-[var(--mentor-primary)] px-4 py-2 text-sm text-white">
                    Add
                </button>
            @endif
        </div>

        <div class="mt-4 space-y-2">
            @forelse($materials as $material)
                <button type="button"
                        wire:key="material-{{ $material->id }}"
                        wire:click="selectMaterial(@js($material->id))"
                        class="w-full rounded-xl border p-4 text-left transition {{ $selectedMaterial?->id === $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white' : 'border-slate-200' }}">
                    <div class="truncate text-sm font-medium">
                        #{{ $material->sort_order }} · {{ $material->name }}
                    </div>
                    <div class="mt-1 text-xs">
                        {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}
                    </div>
                </button>
            @empty
                <div class="rounded-xl border border-dashed border-slate-200 p-5 text-sm">
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
                            <label class="text-xs font-medium">Name</label>
                            <input wire:model="materialName" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                        </div>

                        <div>
                            <label class="text-xs font-medium">Type</label>
                            <select wire:model.live="materialType" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                                @foreach($materialTypeOptions as $type)
                                    <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium">Status</label>
                            <select wire:model="materialStatus" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        @if(in_array($materialType, ['pdf', 'ppt'], true))
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium">File</label>
                                <input type="file" wire:model="materialFile" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                            </div>
                        @endif

                        @if($materialType === 'video')
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium">Video URL</label>
                                <input wire:model.live.debounce.500ms="materialExternalUrl" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                            </div>
                        @endif

                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium">Sort Order</label>
                            <input wire:model="materialSortOrder" type="number" min="0" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                        </div>
                    </div>

                    @error('materialName') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialType') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialFile') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialStatus') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialExternalUrl') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('materialSortOrder') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" wire:click="closeMaterialModal" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-[var(--mentor-primary)] px-4 py-2 text-sm font-medium text-white">
                            {{ $editingMaterialId ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>