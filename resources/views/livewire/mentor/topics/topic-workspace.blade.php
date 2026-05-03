<div class="space-y-6">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 shadow-sm">
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
                <a href="{{ route('topics.show', $topic->slug) }}"
                   class="px-4 py-2 rounded-xl border text-sm">
                    Student View
                </a>
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

        <div class="mt-6 flex flex-wrap gap-2">
            <button wire:click="setTab('overview')"
                    class="px-4 py-2 rounded-xl border {{ $tab === 'overview' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Overview
            </button>
            <button wire:click="setTab('materials')"
                    class="px-4 py-2 rounded-xl border {{ $tab === 'materials' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Materials
            </button>
            <button wire:click="setTab('students')"
                    class="px-4 py-2 rounded-xl border {{ $tab === 'students' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Students
            </button>
            <button wire:click="setTab('assessment')"
                    class="px-4 py-2 rounded-xl border {{ $tab === 'assessment' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Assessment
            </button>
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
        <section class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
            <div class="space-y-4">
                <div class="rounded-2xl bg-white border p-5">
                    <div class="flex items-end justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold">Materials</h2>
                            <p class="text-sm text-slate-500">Tambah, lihat, dan hapus materi pada topic ini.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">
                        @foreach($materials as $material)
                            <button wire:click="selectMaterial('{{ $material->id }}')"
                                    class="shrink-0 w-[280px] text-left rounded-2xl border p-5 transition snap-start
                                    {{ $selectedMaterial?->id === $material->id ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:border-slate-400' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-semibold">{{ $material->name }}</div>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $selectedMaterial?->id === $material->id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ strtoupper($material->type) }}
                                    </span>
                                </div>

                                <p class="text-sm mt-3 {{ $selectedMaterial?->id === $material->id ? 'text-slate-300' : 'text-slate-500' }}">
                                    {{ \Illuminate\Support\Str::limit($material->external_url ?: $material->path ?: '-', 90) }}
                                </p>

                                <div class="mt-4 text-xs flex items-center justify-between">
                                    <span>{{ $material->visibility }}</span>
                                    <span>{{ $material->status }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white border p-5 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold">{{ $selectedMaterial?->name ?? 'No material selected' }}</h2>
                            <p class="text-sm text-slate-500">
                                {{ $selectedMaterial ? strtoupper($selectedMaterial->type) . ' · ' . $selectedMaterial->visibility : 'Pilih material di daftar atas.' }}
                            </p>
                        </div>

                        @if($selectedMaterial)
                            <button wire:click="deleteMaterial('{{ $selectedMaterial->id }}')"
                                    class="px-4 py-2 rounded-xl border text-sm text-rose-600">
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
                            <div class="rounded-2xl border p-5 bg-slate-50">
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

            <aside class="rounded-2xl bg-white border p-5 space-y-4 h-fit">
                <h2 class="text-lg font-semibold">Add Material</h2>

                <input wire:model="materialName" class="w-full border rounded-xl px-4 py-2" placeholder="Material name">

                <select wire:model="materialType" class="w-full border rounded-xl px-4 py-2">
                    <option value="pdf">PDF</option>
                    <option value="video">Video</option>
                    <option value="doc">Document</option>
                    <option value="ppt">Presentation</option>
                    <option value="audio">Audio</option>
                    <option value="image">Image</option>
                    <option value="link">Link</option>
                </select>

                <select wire:model="materialVisibility" class="w-full border rounded-xl px-4 py-2">
                    <option value="Public">Public</option>
                    <option value="Private">Private</option>
                </select>

                <select wire:model="materialStatus" class="w-full border rounded-xl px-4 py-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>

                <input wire:model="materialSortOrder" type="number" class="w-full border rounded-xl px-4 py-2" placeholder="Sort order">

                <input wire:model="materialExternalUrl" class="w-full border rounded-xl px-4 py-2" placeholder="External URL (optional)">

                <input wire:model="materialFile" type="file" class="w-full border rounded-xl px-4 py-2">

                @error('materialFile')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror

                <button wire:click="saveMaterial" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white">
                    Save Material
                </button>
            </aside>
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