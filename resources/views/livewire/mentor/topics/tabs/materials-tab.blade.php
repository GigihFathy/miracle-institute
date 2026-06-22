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

        </div>

        <div class="flex w-full flex-grow flex-col">
            @if($selectedMaterial)
                @if($selectedMaterial->type === 'video' && $selectedMaterial->external_url)
                    <div class="mx-auto flex w-full max-w-3xl flex-col space-y-5">
                        @if($videoEmbedUrl)
                            <div class="aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary)] shadow-md">
                                <iframe
                                    src="{{ $videoEmbedUrl }}"
                                    title="{{ $selectedMaterial->name }}"
                                    class="h-full w-full"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a
                                    href="{{ $selectedMaterial->external_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="admin-neutral-button inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm"
                                >
                                    {{ __('mentor.topic_tabs.materials.preview.watch_youtube') }}
                                </a>
                            </div>
                        @elseif($videoThumbnailUrl)
                            <div class="group relative aspect-video w-full overflow-hidden rounded-2xl border border-slate-200 bg-[var(--mentor-primary)] shadow-md">
                                <img src="{{ $videoThumbnailUrl }}" class="h-full w-full object-cover opacity-80" alt="{{ $selectedMaterial->name }}">
                            </div>
                        @endif
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
        </div>

        <div class="mt-4 space-y-2">
            @forelse($materials as $material)
                <button type="button"
                        wire:key="material-{{ $material->id }}"
                        wire:click="selectMaterial(@js($material->id))"
                        class="w-full rounded-xl border p-4 text-left transition {{ $selectedMaterial?->id === $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-[var(--mentor-primary-soft-2)] text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }}">
                    <div class="truncate text-sm font-medium">
                        #{{ $material->sort_order }} Â· {{ $material->name }}
                    </div>
                    <div class="mt-1 text-xs">
                        {{ strtoupper($material->type) }} Â· {{ ucfirst($material->status) }}
                    </div>
                </button>
            @empty
                <div class="mentor-workspace-empty min-h-0">
                    No materials
                </div>
            @endforelse
        </div>
    </aside>

</section>
