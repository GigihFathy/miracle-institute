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
                                Topic Studio
                            </h1>

                            <a href="{{ route('mentor.topics.index') }}"
                               class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                            Kelola topic secara visual, cepat, dan terhubung langsung ke struktur course.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button
                        wire:click="create"
                        class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Topic
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
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search topic..."
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900"
            >

            <select
                wire:model.live="courseFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>

            <select
                wire:model.live="teacherFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">All teachers</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->full_name }}
                    </option>
                @endforeach
            </select>

            <select
                wire:model.live="statusFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- TOPIC GRID --}}
        <div class="space-y-5 min-w-0">
            @if($rows->isEmpty())
                <x-ui.empty-state
                    title="No topic found"
                    description="Tidak ada topic yang cocok dengan filter saat ini."
                />
            @else
                <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-5">
                    @foreach($rows as $row)
                        <div
                            wire:key="topic-{{ $row->id }}"
                            class="group rounded-[26px] border bg-white shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden min-w-0">

                            <button
                                type="button"
                                wire:click="selectTopic('{{ $row->id }}')"
                                class="w-full text-left block">
                                {{-- HEADER --}}
                                <div class="relative h-48 overflow-hidden bg-slate-100">
                                    <img
                                        src="{{ $row->poster ? asset($row->poster) : asset('images/test.png') }}"
                                        alt="{{ $row->name }}"
                                        class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                                    <div class="absolute inset-x-5 bottom-5 flex items-end justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-200 truncate">
                                                {{ $row->course?->title }}
                                            </div>

                                            <h3 class="mt-1 text-xl font-semibold text-white leading-tight line-clamp-2">
                                                {{ $row->name }}
                                            </h3>
                                        </div>

                                        <span class="shrink-0 rounded-full bg-white/15 backdrop-blur px-3 py-1 text-[11px] text-white border border-white/15">
                                            {{ ucfirst($row->status) }}
                                        </span>
                                    </div>
                                </div>
                            </button>

                            {{-- BODY --}}
                            <div class="p-5 space-y-5 min-w-0">
                                <p class="text-sm text-slate-600 leading-6 line-clamp-3 break-words min-h-[4.5rem]">
                                    {{ $row->description }}
                                </p>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-xl bg-slate-50 border p-3">
                                        <div class="text-[11px] text-slate-500">Materials</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $row->materials_count }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 border p-3">
                                        <div class="text-[11px] text-slate-500">Sessions</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $row->video_sessions_count }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 border p-3">
                                        <div class="text-[11px] text-slate-500">Duration</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $row->material_duration_text ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 border p-3">
                                        <div class="text-[11px] text-slate-500">Completion</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $row->completion_rate }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 pt-1">
                                    <a
                                        href="{{ route('mentor.materials.index', ['topicFilter' => $row->id]) }}"
                                        class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-medium">
                                        Materials
                                    </a>

                                    <a
                                        href="{{ route('mentor.sessions.index', ['topicFilter' => $row->id]) }}"
                                        class="px-3 py-2 rounded-xl border text-xs">
                                        Sessions
                                    </a>

                                    <a
                                        href="{{ route('mentor.assessments.index', ['courseFilter' => $row->course_id]) }}"
                                        class="px-3 py-2 rounded-xl border text-xs">
                                        Assessments
                                    </a>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] border {{ $row->assessment_badge_class }}">
                                        {{ $row->assessment_label }}
                                    </span>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            wire:click.stop="edit('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-medium">
                                            Edit
                                        </button>

                                        <button
                                            wire:click.stop="delete('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4 sticky top-24 h-fit min-w-0">
            <div class="rounded-[28px] bg-white border shadow-sm overflow-hidden">
                @if($selectedTopic)
                    <div class="p-6 space-y-5 min-w-0">
                        <div class="space-y-2 min-w-0">
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

                        <div class="aspect-video rounded-2xl overflow-hidden border bg-slate-100">
                            <img
                                src="{{ $selectedTopic->poster ? asset($selectedTopic->poster) : asset('images/test.png') }}"
                                class="w-full h-full object-cover"
                                alt="{{ $selectedTopic->name }}">
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Course</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedTopic->course?->title }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Teacher</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedTopic->teacher?->full_name }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Materials</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedTopic->materials_count }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Sessions</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedTopic->video_sessions_count }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Completion</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedTopic->completion_rate }}%
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Duration</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedTopic->material_duration_text ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3 min-w-0">
                            <div class="text-sm font-semibold">Assessment status</div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] border {{ $selectedTopic->assessment_badge_class }}">
                                    {{ $selectedTopic->assessment_label }}
                                </span>

                                <span class="text-xs text-slate-500">
                                    Assessment belongs to the course, not the topic.
                                </span>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 leading-7 break-words">
                            {{ $selectedTopic->description }}
                        </p>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3 min-w-0">
                            <div class="text-sm font-semibold">Quick actions</div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('mentor.materials.index', ['topicFilter' => $selectedTopic->id]) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs text-center">
                                    Materials
                                </a>

                                <a href="{{ route('mentor.sessions.index', ['topicFilter' => $selectedTopic->id]) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Sessions
                                </a>

                                <a href="{{ route('mentor.assessments.index', ['courseFilter' => $selectedTopic->course_id]) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Assessments
                                </a>

                                <a href="{{ route('topics.show', $selectedTopic->slug) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Public view
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-10">
                        <x-ui.empty-state
                            title="Select a topic"
                            description="Klik salah satu topic card untuk melihat detail dan shortcut manajemen."
                        />
                    </div>
                @endif
            </div>
        </aside>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="showModal"
        :title="$editingId ? 'Edit Topic' : 'New Topic'"
        description="Studio form untuk topic."
        maxWidth="max-w-3xl"
    >
        <div class="space-y-4">
            <select wire:model="course_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model="teacher_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                @endforeach
            </select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="name" class="w-full border rounded-xl px-4 py-3" placeholder="Name">
                <input wire:model="category" class="w-full border rounded-xl px-4 py-3" placeholder="Category">
            </div>

            <input wire:model="poster" class="w-full border rounded-xl px-4 py-3" placeholder="Poster path/url">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select wire:model="visibility" class="w-full border rounded-xl px-4 py-3">
                    <option value="Public">Public</option>
                    <option value="Private">Private</option>
                </select>

                <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <input wire:model="sort_order" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Sort order">
            <textarea wire:model="description" rows="5" class="w-full border rounded-xl px-4 py-3" placeholder="Description"></textarea>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <button type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-xl border">
                    Cancel
                </button>

                <button wire:click="save"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white">
                    Save
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>

</div>