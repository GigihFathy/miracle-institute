<div @class(['space-y-6', 'lg:px-36'])>
    <section class="rounded-3xl bg-white border px-6 pt-6 sm:pt-8 sm:px-8 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    Mentor Workspace · {{ $topic->course?->title }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold leading-tight">
                    {{ $topic->name }}
                </h1>
                <p class="text-slate-600 max-w-3xl">
                    Workspace untuk menambah materi, melihat daftar siswa, dan mengelola pembelajaran pada topik ini.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('mentor.topics.index') }}"
                   class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                    Back to Topics
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
            <div class="rounded-2xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Materials</div>
                <div class="font-semibold mt-1">{{ $materials->count() }}</div>
            </div>
            <div class="rounded-2xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Students</div>
                <div class="font-semibold mt-1">{{ $students->count() }}</div>
            </div>
            <div class="rounded-2xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Assessments</div>
                <div class="font-semibold mt-1">{{ $assessment ? 1 : 0 }}</div>
            </div>
            <div class="rounded-2xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Your Role</div>
                <div class="font-semibold mt-1">Mentor</div>
            </div>
        </div>

        <div class="mt-6 border-b border-slate-200">
            <div class="-mb-px flex gap-1 overflow-x-auto">
            <button type="button" wire:click="setTab('overview')"
                class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $tab === 'overview' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Overview
            </button>
            <button type="button" wire:click="setTab('materials')"
                class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $tab === 'materials' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Materials
            </button>
            <button type="button" wire:click="setTab('students')"
                class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $tab === 'students' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Students
            </button>
            <button type="button" wire:click="setTab('assessment')"
                class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition {{ $tab === 'assessment' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Assessment
            </button>
            </div>
        </div>
    </section>

    @if($tab === 'overview')
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <h2 class="text-lg font-semibold">Topic Overview</h2>
                <p class="text-sm text-slate-600">{{ $topic->description }}</p>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl border p-3">
                        <div class="text-xs text-slate-500">Category</div>
                        <div class="font-semibold mt-1">{{ $topic->category ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl border p-3">
                        <div class="text-xs text-slate-500">Visibility</div>
                        <div class="font-semibold mt-1">{{ $topic->visibility }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <h2 class="text-lg font-semibold">Assessment Info</h2>

                @if($assessment)
                    <div class="space-y-3 text-sm">
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Title</div>
                            <div class="font-semibold mt-1">{{ $assessment->title }}</div>
                        </div>
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Passing Grade</div>
                            <div class="font-semibold mt-1">{{ $assessment->passing_grade }}</div>
                        </div>
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Time Limit</div>
                            <div class="font-semibold mt-1">{{ $assessment->time_limit_minutes ?? 'No limit' }}</div>
                        </div>
                    </div>
                @else
                    <x-ui.empty-state
                        title="Belum ada assessment"
                        description="Assessment untuk topic ini belum tersedia."
                    />
                @endif
            </div>
        </section>
    @endif

    @if($tab === 'materials')
        <section x-data="{ addMaterialOpen: false }" class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6 xl:items-stretch">
            <div class="space-y-4 h-full">
                <div class="rounded-2xl bg-white border p-5 space-y-4 h-full flex flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold">Materials</h2>
                            <p class="text-sm text-slate-500">Tambah, lihat, dan hapus materi pada topic ini.</p>
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">{{ $selectedMaterial?->name ?? 'No material selected' }}</h2>
                                    <p class="text-sm text-slate-500">
                                        {{ $selectedMaterial ? strtoupper($selectedMaterial->type) . ' · ' . $selectedMaterial->visibility : 'Pilih material di daftar kanan.' }}
                                    </p>
                                </div>

                                @if($selectedMaterial)
                                    <button type="button"
                                            x-on:click.prevent="if (confirm('Delete this material?')) { $wire.deleteMaterial('{{ $selectedMaterial->id }}') }"
                                            class="px-4 py-2 rounded-xl border text-sm text-rose-600 hover:bg-rose-50 transition">
                                        Delete
                                    </button>
                                @endif
                            </div>

                            @if($selectedMaterial && $materialPreviewUrl)
                                @if($selectedMaterial->type === 'video')
                                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100">
                                        <iframe src="{{ $materialPreviewUrl }}" class="w-full h-full" allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="rounded-2xl border border-slate-200 p-5 bg-slate-50">
                                        <a href="{{ $materialPreviewUrl }}" target="_blank" class="text-slate-900 underline">
                                            Open / download material
                                        </a>
                                    </div>
                                @endif
                            @else
                                <x-ui.empty-state
                                    title="No preview"
                                    description="Material yang dipilih belum memiliki preview."
                                />
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <aside class="rounded-2xl bg-white border p-5 space-y-4 h-full self-stretch flex flex-col">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Materials List</h2>
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">{{ $materials->count() }}</span>
                </div>

                <div class="flex-1 space-y-2">
                    @forelse($materials as $material)
                        <button wire:click="selectMaterial('{{ $material->id }}')"
                                class="w-full text-left rounded-2xl border p-4 transition
                                {{ $selectedMaterial?->id === $material->id ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:border-slate-400' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 font-semibold truncate">{{ $material->name }}</div>
                                <span class="text-[11px] px-2 py-1 rounded-full {{ $selectedMaterial?->id === $material->id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600' }}">
                                    {{ strtoupper($material->type) }}
                                </span>
                            </div>

                            <div class="mt-2 text-xs {{ $selectedMaterial?->id === $material->id ? 'text-slate-300' : 'text-slate-500' }}">
                                {{ \Illuminate\Support\Str::limit($material->external_url ?: $material->path ?: '-', 90) }}
                            </div>
                        </button>
                    @empty
                        <div class="flex h-full min-h-60 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-center">
                            <div class="text-sm font-medium text-slate-900">Belum memiliki material</div>
                            <p class="mt-2 text-sm text-slate-500">
                                Tambahkan material pertama untuk topik ini.
                            </p>
                            <button type="button"
                                    @click="addMaterialOpen = true"
                                    class="mt-5 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                                Add Material
                            </button>
                        </div>
                    @endforelse
                    @if($materials->isNotEmpty())
                        <div class="mt-6 pt-4 border-slate-100">
                            <button type="button"
                                    @click="addMaterialOpen = true"
                                    class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                                Add Material
                            </button>
                        </div>
                    @endif
                </div>
            </aside>

            <div x-cloak x-show="addMaterialOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @keydown.escape.window="addMaterialOpen = false"
                 @click.self="addMaterialOpen = false"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
                <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                        <div>
                            <h3 class="text-lg font-semibold">Add Material</h3>
                            <p class="text-sm text-slate-500">Tambahkan materi baru ke topic ini.</p>
                        </div>

                        <button type="button" @click="addMaterialOpen = false" class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Close
                        </button>
                    </div>

                    <div class="grid gap-4 px-6 py-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <input wire:model="materialName" class="w-full rounded-xl border px-4 py-2" placeholder="Material name">
                        </div>

                        <select wire:model="materialType" class="w-full rounded-xl border px-4 py-2">
                            <option value="pdf">PDF</option>
                            <option value="video">Video</option>
                            <option value="doc">Document</option>
                            <option value="ppt">Presentation</option>
                            <option value="audio">Audio</option>
                            <option value="image">Image</option>
                            <option value="link">Link</option>
                        </select>

                        <select wire:model="materialVisibility" class="w-full rounded-xl border px-4 py-2">
                            <option value="Public">Public</option>
                            <option value="Private">Private</option>
                        </select>

                        <select wire:model="materialStatus" class="w-full rounded-xl border px-4 py-2">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>

                        <input wire:model="materialSortOrder" type="number" class="w-full rounded-xl border px-4 py-2" placeholder="Sort order">

                        <div class="sm:col-span-2">
                            <input wire:model="materialExternalUrl" class="w-full rounded-xl border px-4 py-2" placeholder="External URL (optional)">
                        </div>

                        <div class="sm:col-span-2">
                            <input wire:model="materialFile" type="file" class="w-full rounded-xl border px-4 py-2">
                        </div>

                        <div class="sm:col-span-2">
                            @error('materialFile')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                        <button type="button" @click="addMaterialOpen = false" class="rounded-xl border px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Cancel
                        </button>

                        <button type="button"
                                wire:click="saveMaterial"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                            Save Material
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($tab === 'students')
        <section class="rounded-2xl bg-white border p-5 space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Students</h2>
                    <p class="text-sm text-slate-500">Lihat progress student pada topik ini.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="p-4">Student</th>
                            <th class="p-4">Progress</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $row)
                            <tr class="border-t">
                                <td class="p-4 font-medium">
                                    {{ $row['enrollment']->user?->full_name }}
                                </td>
                                <td class="p-4">
                                    <div class="w-full max-w-xs h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-2 rounded-full bg-slate-900" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-2">{{ $row['percent'] }}%</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100">
                                        {{ strtoupper($row['status']) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    {{ $row['progress']?->updated_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-500">
                                    No students yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if($tab === 'assessment')
        <section class="rounded-2xl bg-white border p-5 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Assessment Snapshot</h2>
                    <p class="text-sm text-slate-500">Info read-only untuk mentor.</p>
                </div>
            </div>

            @if($assessment)
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl border p-4 bg-slate-50">
                        <div class="text-xs text-slate-500">Title</div>
                        <div class="font-semibold mt-1">{{ $assessment->title }}</div>
                    </div>
                    <div class="rounded-xl border p-4 bg-slate-50">
                        <div class="text-xs text-slate-500">Passing Grade</div>
                        <div class="font-semibold mt-1">{{ $assessment->passing_grade }}</div>
                    </div>
                    <div class="rounded-xl border p-4 bg-slate-50">
                        <div class="text-xs text-slate-500">Time Limit</div>
                        <div class="font-semibold mt-1">{{ $assessment->time_limit_minutes ?? 'No limit' }}</div>
                    </div>
                    <div class="rounded-xl border p-4 bg-slate-50">
                        <div class="text-xs text-slate-500">Question Limit</div>
                        <div class="font-semibold mt-1">{{ $assessment->question_limit ?? 'All' }}</div>
                    </div>
                </div>
            @else
                <x-ui.empty-state
                    title="No assessment"
                    description="Topik ini belum memiliki assessment."
                />
            @endif
        </section>
    @endif
</div>