<div class="space-y-6 lg:px-5 2xl:px-8 pb-10 scale-[0.90] origin-top">

    {{-- HERO --}}
    <section class="rounded-[28px] border bg-white shadow-sm overflow-hidden">
        <div class="p-7 lg:p-10 space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-3 max-w-4xl min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Disciples Studio
                    </div>

                    <div class="space-y-3 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                                Material Studio
                            </h1>

                            <a href="{{ route('mentor.topics.index') }}"
                               class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                            Pengelolaan material yang visual, dengan ringkasan topic, tipe file, ukuran data, estimasi waktu baca, dan shortcut penginputan cepat.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Material
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            @php
                $statRows = collect($statsCards)->chunk(3);
            @endphp

            <div class="space-y-3">
                @foreach($statRows as $row)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ $row->count() === 3 ? 'xl:grid-cols-3' : ($row->count() === 2 ? 'xl:grid-cols-2' : 'xl:grid-cols-1') }}">
                        @foreach($row as $card)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 min-w-0">
                                <div class="text-xs text-slate-500">
                                    {{ $card['label'] }}
                                </div>

                                <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900 break-words">
                                    {{ $card['value'] }}
                                </div>

                                <div class="mt-1 text-xs leading-5 text-slate-500 break-words">
                                    {{ $card['note'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            <input wire:model.live.debounce.300ms="search"
                   class="w-full border rounded-xl px-4 py-3"
                   placeholder="Search material...">

            <select wire:model.live="courseFilter" class="border rounded-xl px-4 py-3">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model.live="topicFilter" class="border rounded-xl px-4 py-3">
                <option value="">All topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->course?->title }} · {{ $topic->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="typeFilter" class="border rounded-xl px-4 py-3">
                <option value="">All type</option>
                <option value="pdf">PDF</option>
                <option value="ppt">PPT</option>
                <option value="video">VIDEO</option>
            </select>

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-3">
                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
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

                            <button type="button"
                                    wire:click="toggleTopic('{{ $topic->id }}')"
                                    class="w-full text-left p-5 flex flex-wrap items-center justify-between gap-3">
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
                            </button>

                            @if(in_array($topic->id, $openTopics, true))
                                <div class="border-t p-5 bg-slate-50">
                                    @if($topic->materials->isEmpty())
                                        <x-ui.empty-state
                                            title="No materials yet"
                                            description="Topic ini belum memiliki material."
                                        />
                                    @else
                                        <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-4">
                                            @foreach($topic->materials as $material)
                                                @php
                                                    $openUrl = $material->external_url ?: ($material->path ? Storage::disk('public')->url($material->path) : null);
                                                    $size = $this->resolveStoredSize($material->path);
                                                    $sizeText = $size ? $this->formatBytes($size) : 'N/A';
                                                    $readTime = $size ? $this->formatMinutes($this->estimateReadTimeMinutes($size, $material->type)) : 'N/A';
                                                    $typeBadge = match ($material->type) {
                                                        'pdf' => 'bg-red-50 text-red-700 border-red-200',
                                                        'ppt' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                        'video' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                                                    };
                                                @endphp

                                                <div wire:key="material-{{ $material->id }}"
                                                     class="rounded-2xl border bg-white p-4 min-w-0">
                                                    <div class="flex items-start justify-between gap-3 min-w-0">
                                                        <div class="min-w-0">
                                                            <div class="font-semibold break-words">
                                                                {{ $material->name }}
                                                            </div>
                                                            <div class="text-xs text-slate-500 mt-1">
                                                                {{ $material->visibility }}
                                                            </div>
                                                        </div>

                                                        <span class="shrink-0 text-xs px-2 py-1 rounded-full border {{ $typeBadge }}">
                                                            {{ strtoupper($material->type) }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                                        <div class="rounded-xl border bg-slate-50 p-3">
                                                            <div class="text-slate-500">Size</div>
                                                            <div class="mt-1 font-semibold text-slate-900">
                                                                {{ $sizeText }}
                                                            </div>
                                                        </div>

                                                        <div class="rounded-xl border bg-slate-50 p-3">
                                                            <div class="text-slate-500">Read time</div>
                                                            <div class="mt-1 font-semibold text-slate-900">
                                                                {{ $readTime }}
                                                            </div>
                                                        </div>

                                                        <div class="rounded-xl border bg-slate-50 p-3">
                                                            <div class="text-slate-500">Sort</div>
                                                            <div class="mt-1 font-semibold text-slate-900">
                                                                {{ $material->sort_order }}
                                                            </div>
                                                        </div>

                                                        <div class="rounded-xl border bg-slate-50 p-3">
                                                            <div class="text-slate-500">Status</div>
                                                            <div class="mt-1 font-semibold text-slate-900">
                                                                {{ ucfirst($material->status) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 text-xs text-slate-500 space-y-1 break-words">
                                                        <div>
                                                            Topic: {{ $topic->course?->title }} · {{ $topic->name }}
                                                        </div>

                                                        <div>
                                                            Source:
                                                            {{ $material->external_url ?: $material->path ?: 'No file attached' }}
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 flex flex-wrap gap-2">
                                                        @if($openUrl)
                                                            <a href="{{ $openUrl }}" target="_blank"
                                                               class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs">
                                                                Open
                                                            </a>
                                                        @endif

                                                        <button wire:click="edit('{{ $material->id }}')"
                                                                class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs">
                                                            Edit
                                                        </button>

                                                        <button wire:click="delete('{{ $material->id }}')"
                                                                class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        <button wire:click="create('{{ $topic->id }}')"
                                                class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                            + Add material to this topic
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div>
                    {{ $topics->links() }}
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4 sticky top-24 h-fit min-w-0">
            <div class="rounded-[28px] bg-white border shadow-sm overflow-hidden">
                @if($selectedTopic)
                    <div class="p-6 space-y-5 min-w-0">
                        <div class="space-y-2">
                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                                Selected Topic
                            </div>

                            <h2 class="text-2xl font-bold tracking-tight text-slate-900 leading-tight break-words">
                                {{ $selectedTopic->name }}
                            </h2>

                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedTopic->course?->title }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedTopic->teacher?->full_name }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ ucfirst($selectedTopic->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Materials</div>
                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedTopic->materials_count }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Sessions</div>
                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedTopic->video_sessions_count }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Total size</div>
                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedTopic->total_material_size ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Read time</div>
                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedTopic->estimated_read_time ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Assessment status</div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] border {{ $selectedTopic->assessment_badge_class }}">
                                    {{ $selectedTopic->assessment_label }}
                                </span>

                                <span class="text-xs text-slate-500">
                                    Assessment melekat ke course utama.
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Description</div>
                            <p class="mt-2 text-sm leading-7 text-slate-600 break-words">
                                {{ $selectedTopic->description }}
                            </p>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Quick actions</div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('mentor.sessions.index', ['topicFilter' => $selectedTopic->id]) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs text-center">
                                    Sessions
                                </a>

                                <a href="{{ route('mentor.assessments.index', ['courseFilter' => $selectedTopic->course_id]) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Course assessment
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-10">
                        <x-ui.empty-state
                            title="Select a topic"
                            description="Klik topic untuk melihat detail dan daftar material."
                        />
                    </div>
                @endif
            </div>
        </aside>
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