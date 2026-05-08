<div class="space-y-6 lg:px-36 pb-10">

    {{-- HERO --}}
    <section class="rounded-3xl bg-white border px-6 py-6 sm:py-8 sm:px-8 shadow-sm">
        <div class="space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-3 max-w-4xl min-w-0">

                    <div class="space-y-3 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h1 class="text-2xl sm:text-3xl font-bold leading-tight text-slate-900">
                                Material Studio
                            </h1>
                        </div>

                        <p class="text-slate-600 max-w-3xl">
                            Pengelolaan material yang visual, dengan ringkasan topic, tipe file, ukuran data, estimasi waktu baca, dan shortcut penginputan cepat.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="px-4 py-2 rounded-xl border text-sm hover:bg-slate-50 transition">
                        + New Material
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl bg-white border p-4 space-y-4">
        <div class="border-b border-slate-200">
            <div class="-mb-px flex gap-1 overflow-x-auto">
                <button type="button" wire:click="setTypeTab('pdf')"
                    class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $typeFilter === 'pdf' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    PDF
                </button>
                <button type="button" wire:click="setTypeTab('ppt')"
                    class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $typeFilter === 'ppt' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    PPT
                </button>
                <button type="button" wire:click="setTypeTab('video')"
                    class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $typeFilter === 'video' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    Video
                </button>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- MATERIAL LIST --}}
        <div class="space-y-4 min-w-0">
            @if($topics->isEmpty())
                <x-ui.empty-state
                    title="No material source found"
                    description="Tidak ada topic yang cocok dengan filter saat ini."
                />
            @else
                <div class="space-y-4">
                    @foreach($topics as $topic)
                        <div wire:key="material-topic-{{ $topic->id }}"
                             class="rounded-[26px] border bg-white shadow-sm overflow-hidden">
                            <div class="p-5 flex flex-wrap items-center justify-between gap-3 border-b bg-slate-50/50">
                                <div class="min-w-0">
                                    <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400 truncate">
                                        {{ $topic->course?->title }}
                                    </div>
                                    <div class="text-xl font-semibold mt-1 break-words">
                                        {{ $topic->name }}
                                    </div>
                                    <div class="text-sm text-slate-500 mt-1 break-words">
                                        {{ $topic->teacher?->full_name }}
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 shrink-0">
                                    <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                        {{ $topic->materials_count }} material(s)
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                        {{ $topic->total_material_size ?? 'N/A' }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                        {{ ucfirst($topic->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 bg-white">
                                @if($topic->materials->isEmpty())
                                    <x-ui.empty-state
                                        title="No materials yet"
                                        description="Topic ini belum memiliki material."
                                    />
                                @else
                                    <div class="space-y-4">
                                        @foreach($topic->materials as $material)
                                            @php
                                                $openUrl = $this->resolveOpenUrl($material->external_url, $material->path);
                                                $typeBadge = match ($material->type) {
                                                    'pdf' => 'bg-red-50 text-red-700 border-red-200',
                                                    'ppt' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                    'video' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                                };
                                            @endphp

                                            <div wire:key="material-{{ $material->id }}" class="rounded-2xl border bg-white p-4 min-w-0">
                                                <div class="flex items-start justify-between gap-3 min-w-0">
                                                    <div class="min-w-0">
                                                        <div class="font-semibold break-words">{{ $material->name }}</div>
                                                        <div class="text-xs text-slate-500 mt-1">{{ $material->visibility }} - {{ ucfirst($material->status) }}</div>
                                                    </div>
                                                    <span class="shrink-0 text-xs px-2 py-1 rounded-full border {{ $typeBadge }}">
                                                        {{ strtoupper($material->type) }}
                                                    </span>
                                                </div>

                                                <div class="mt-3">
                                                    @if($material->type === 'video' && $openUrl)
                                                        <div class="aspect-video rounded-xl overflow-hidden bg-slate-100 border">
                                                            <iframe src="{{ $openUrl }}" class="w-full h-full" allowfullscreen></iframe>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-slate-500 break-all">
                                                            {{ $material->external_url ?: $material->path ?: 'No file attached' }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-4 flex flex-wrap gap-2">
                                                    @if($openUrl)
                                                        <a href="{{ $openUrl }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs">
                                                            Lihat Material
                                                        </a>
                                                    @endif
                                                    <button wire:click="edit('{{ $material->id }}')" class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs">
                                                        Edit
                                                    </button>
                                                    <button wire:click="delete('{{ $material->id }}')" class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <button wire:click="create('{{ $topic->id }}')" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                        + Add material to this topic
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="showModal"
        :title="$editingId ? 'Edit Material' : 'New Material'"
        description="Studio form untuk material."
        maxWidth="max-w-3xl"
    >
        <form wire:submit.prevent="save" class="space-y-4">
            <select wire:model="topic_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select topic</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->course?->title }} · {{ $topic->name }}</option>
                @endforeach
            </select>
            @error('topic_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <select wire:model="uploader_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select uploader</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }} · {{ $user->email }}</option>
                @endforeach
            </select>
            @error('uploader_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="name" class="w-full border rounded-xl px-4 py-3" placeholder="Name">
                <select wire:model="type" class="w-full border rounded-xl px-4 py-3">
                    <option value="pdf">PDF</option>
                    <option value="ppt">PPT</option>
                    <option value="video">VIDEO</option>
                </select>
            </div>
            @error('name') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            @error('type') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="path" class="w-full border rounded-xl px-4 py-3" placeholder="File path">
                <input wire:model="external_url" class="w-full border rounded-xl px-4 py-3" placeholder="External URL">
            </div>
            @error('path') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            @error('external_url') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="space-y-2">
                <div class="text-xs text-slate-500">Upload file (optional)</div>
                <input type="file" wire:model="uploadFile" class="w-full border rounded-xl px-4 py-3 bg-white">
                <div wire:loading wire:target="uploadFile" class="text-xs text-slate-500">
                    Uploading file...
                </div>
                @error('uploadFile') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select wire:model="visibility" class="w-full border rounded-xl px-4 py-3">
                    <option value="Public">Public</option>
                    <option value="Private">Private</option>
                </select>

                <input wire:model="sort_order" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Sort order">
            </div>

            <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <button type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-xl border">
                    Cancel
                </button>

                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save,uploadFile"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="save,uploadFile">Save</span>
                    <span wire:loading wire:target="save,uploadFile">Saving...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>