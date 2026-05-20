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
                    @php
                        $url = $selectedMaterial->external_url;
                        $ytId = null;

                        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|shorts\/)([^"&?\/\s]{11})/i', $url, $match)) {
                            $ytId = $match[1];
                        }

                        $finalThumbnailUrl = $ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : null;
                        $finalWatchUrl = $ytId ? "https://www.youtube.com/watch?v={$ytId}" : $url;
                    @endphp

                    <div class="mx-auto flex w-full max-w-3xl flex-col space-y-5">
                        <div class="group relative aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary)] shadow-md">
                            @if($finalThumbnailUrl)
                                <img
                                    src="{{ $finalThumbnailUrl }}"
                                    alt="{{ __('mentor.topic_tabs.materials.thumbnail_alt', ['name' => $selectedMaterial->name]) }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover opacity-80 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100"
                                >
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-[var(--mentor-primary-soft-2)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-10 w-10 text-[color:color-mix(in_oklab,#004777_35%,white)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-[color:color-mix(in_oklab,#004777_55%,white)]">
                                        {{ __('mentor.topic_tabs.materials.thumbnail_unavailable') }}
                                    </span>
                                </div>
                            @endif

                            @if($finalWatchUrl)
                                <a href="{{ $finalWatchUrl }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 flex items-center justify-center">
                                    <div class="grid h-16 w-16 place-items-center rounded-full bg-white/90 text-[var(--mentor-primary)] shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:bg-[var(--mentor-primary)] group-hover:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </a>
                            @endif
                        </div>

                        @if($finalWatchUrl)
                            <div class="flex flex-col items-center gap-3">
                                <a
                                    href="{{ $finalWatchUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-[var(--mentor-primary)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:brightness-110 hover:shadow-md sm:w-auto"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                    {{ __('mentor.topic_tabs.materials.actions.watch_youtube') }}
                                </a>
                                <p class="text-center text-xs text-[color:color-mix(in_oklab,#004777_55%,white)]">
                                    {{ __('mentor.topic_tabs.materials.youtube_hint') }}
                                </p>
                            </div>
                        @endif
                    </div>

                @elseif($materialPreviewUrl)
                    <div class="mx-auto flex w-full max-w-4xl flex-col space-y-5">
                        <div class="aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] shadow-sm">
                            <iframe src="{{ $materialPreviewUrl }}" class="h-full w-full" allowfullscreen></iframe>
                        </div>
                        <div class="flex justify-center">
                            <a href="{{ $materialPreviewUrl }}" target="_blank" class="inline-flex w-full items-center justify-center rounded-xl bg-[var(--mentor-primary)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:brightness-110 hover:shadow-md sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ __('mentor.topic_tabs.materials.actions.open_download') }}
                            </a>
                        </div>
                    </div>

                @else
                    <div class="flex min-h-[250px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-12 w-12 text-[color:color-mix(in_oklab,#004777_35%,white)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">
                            {{ __('mentor.topic_tabs.materials.no_preview') }}
                        </p>
                    </div>
                @endif

            @else
                <div class="flex min-h-[300px] flex-grow flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-6 text-center transition-colors hover:bg-[var(--mentor-primary-soft)]">
                    <div class="mb-4 grid h-16 w-16 place-items-center rounded-full bg-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[color:color-mix(in_oklab,#004777_60%,white)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-[var(--mentor-primary)]">
                        {{ __('mentor.topic_tabs.materials.empty_selected.title') }}
                    </h3>
                    <p class="mt-1 max-w-sm text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                        {{ __('mentor.topic_tabs.materials.empty_selected.subtitle') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_35px_color-mix(in_oklab,#004777_8%,transparent)]">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-[var(--mentor-primary)]">{{ __('mentor.topic_tabs.materials.list.title') }}</h2>

            @if($canAddMaterial)
                <button type="button"
                        wire:click="openMaterialModal"
                        class="admin-primary-button rounded-xl px-4 py-2 text-sm">
                    {{ __('mentor.topic_tabs.materials.list.actions.add') }}
                </button>
            @else
                <span class="rounded-full border border-slate-200 px-3 py-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">
                    {{ __('mentor.topic_tabs.materials.list.limit_reached') }}
                </span>
            @endif
        </div>

        <div class="mt-4 space-y-2">
            @forelse($materials as $material)
                <button type="button"
                        wire:click="selectMaterial('{{ $material->id }}')"
                        class="w-full rounded-xl border p-4 text-left transition
                               {{ $selectedMaterial?->id === $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-sm' : 'border-slate-200 hover:bg-[var(--mentor-primary-soft-2)]' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium">
                                #{{ $material->sort_order }} · {{ $material->name }}
                            </div>
                            <div class="mt-1 text-xs {{ $selectedMaterial?->id === $material->id ? 'text-slate-300' : 'text-[color:color-mix(in_oklab,#004777_70%,white)]' }}">
                                {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}
                            </div>
                        </div>
                    </div>
                </button>
            @empty
                <div class="rounded-xl border border-dashed border-slate-200 bg-[var(--mentor-primary-soft-2)] p-5 text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                    {{ __('mentor.topic_tabs.materials.list.empty') }}
                </div>
            @endforelse
        </div>
    </aside>

    <x-ui.mentor.modal
        :show="$showMaterialModal"
        title="{{ $editingMaterialId ? __('mentor.topic_tabs.materials.modal.edit_title') : __('mentor.topic_tabs.materials.modal.add_title') }}"
        subtitle="{{ __('mentor.topic_tabs.materials.modal.subtitle') }}"
        wire:click="closeMaterialModal"
    >
        <form wire:submit.prevent="saveMaterial" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.name') }}</label>
                    <input wire:model="materialName" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2" placeholder="{{ __('mentor.topic_tabs.materials.form.name_placeholder') }}">
                </div>

                <div>
                    <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.type') }}</label>
                    <select wire:model.live="materialType" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                        <option value="">{{ __('mentor.topic_tabs.materials.form.select') }}</option>
                        @foreach($materialTypeOptions as $type)
                            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                        @endforeach
                    </select>

                    @if(empty($materialTypeOptions))
                        <p class="mt-1 text-xs text-[color:color-mix(in_oklab,#004777_70%,white)]">
                            {{ __('mentor.topic_tabs.materials.form.no_types_left') }}
                        </p>
                    @endif
                </div>

                <div>
                    <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.status') }}</label>
                    <select wire:model="materialStatus" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                        <option value="active">{{ __('mentor.topic_tabs.materials.form.status_active') }}</option>
                        <option value="inactive">{{ __('mentor.topic_tabs.materials.form.status_inactive') }}</option>
                    </select>
                </div>

                @if(in_array($materialType, ['pdf', 'ppt']))
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.file') }}</label>
                        <input type="file" wire:model="materialFile" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2" accept=".pdf,.ppt,.pptx">
                    </div>
                @endif

                @if($materialType === 'video')
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.external_url') }}</label>
                        <input wire:model="materialExternalUrl"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2"
                               placeholder="{{ __('mentor.topic_tabs.materials.form.external_url_placeholder') }}">

                        @if($materialExternalUrl)
                            <div class="mt-2 aspect-video overflow-hidden rounded-2xl bg-[var(--mentor-primary-soft-2)]">
                                <iframe src="{{ app(\App\Services\Materials\MaterialAssetService::class)->youtube->toEmbedUrl($materialExternalUrl) }}"
                                        class="h-full w-full" allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                </iframe>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-[color:color-mix(in_oklab,#004777_60%,white)]">{{ __('mentor.topic_tabs.materials.form.sort_order') }}</label>
                    <input wire:model="materialSortOrder" type="number" min="0" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2">
                </div>
            </div>

            <div wire:loading wire:target="materialFile, saveMaterial" class="mb-5 w-full">
                <div class="flex animate-pulse gap-4 rounded-xl border border-slate-200 bg-[var(--mentor-primary-soft-2)] p-4">
                    <div class="h-10 w-10 rounded-full bg-[var(--mentor-secondary)]"></div>
                    <div class="flex-1 space-y-3 py-1">
                        <div class="h-3 w-3/4 rounded bg-[var(--mentor-secondary)]"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-5/6 rounded bg-[var(--mentor-secondary)]"></div>
                            <div class="h-3 w-1/2 rounded bg-[var(--mentor-secondary)]"></div>
                        </div>
                    </div>
                </div>
            </div>

            @error('materialName') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('materialType') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('materialFile') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('materialStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('materialExternalUrl') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('materialSortOrder') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

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
    </x-ui.mentor.modal>
</section>
