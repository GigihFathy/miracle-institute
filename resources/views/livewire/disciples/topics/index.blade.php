<div class="space-y-8 px-4 sm:px-6 lg:px-36">

    {{-- HERO --}}
    <section class="rounded-2xl border bg-white shadow-sm overflow-hidden">
        <div class="p-6 lg:p-8 space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-2 max-w-4xl min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.3em] text-slate-400">
                        Disciples Studio
                    </div>

                    <div class="space-y-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">
                                Topic Management
                            </h1>

                            <a href="{{ route('mentor.topics.index') }}"
                               class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm text-slate-600 max-w-3xl">
                            Kelola daftar topic dan buka detail saat dibutuhkan.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button
                        wire:click="create"
                        class="h-10 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Topic
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl border bg-white p-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.4a7.25 7.25 0 1 1-14.5 0 7.25 7.25 0 0 1 14.5 0Z" />
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search topic..."
                    class="h-10 w-full rounded-xl border-slate-200 pl-9 pr-3 text-sm focus:border-slate-900 focus:ring-slate-900"
                >
            </div>

            <div class="flex flex-col sm:flex-row gap-3 lg:w-auto">
                <select
                    wire:model.live="courseFilter"
                    class="h-10 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="statusFilter"
                    class="h-10 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                    <option value="">All status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <section>
        <div class="min-w-0">
            @if($rows->isEmpty())
                <x-ui.empty-state
                    title="No topic found"
                    description="Tidak ada topic yang cocok dengan filter saat ini."
                />
            @else
                <div class="rounded-2xl bg-white border overflow-hidden divide-y divide-slate-100">
                    @foreach($rows as $row)
                        <div
                            wire:key="topic-{{ $row->id }}"
                            x-data="{ open: false }"
                            class="group min-w-0">
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full text-left px-4 py-4 hover:bg-slate-50/70 transition block">
                                <div class="flex items-start gap-4">
                                    <img
                                        src="{{ $row->poster ? asset($row->poster) : asset('images/test.png') }}"
                                        alt="{{ $row->name }}"
                                        class="h-16 w-24 rounded-lg object-cover border border-slate-200 shrink-0">

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                {{ $row->name }}
                                            </h3>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $row->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($row->status === 'inactive' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $row->course?->title }} - {{ $row->teacher?->full_name ?? 'No teacher' }}
                                        </div>
                                        <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                                            {{ $row->description }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </button>

                            <div x-show="open" x-collapse class="px-4 pb-4">
                                <div class="ml-0 md:ml-28 border-t border-slate-200 pt-4 space-y-4">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <div class="rounded-lg border p-2.5">
                                        <div class="text-[11px] text-slate-500">Materials</div>
                                            <div class="text-base font-semibold text-slate-900">
                                                {{ $row->materials_count }}
                                            </div>
                                        </div>
                                        <div class="rounded-lg border p-2.5">
                                            <div class="text-[11px] text-slate-500">Sessions</div>
                                            <div class="text-base font-semibold text-slate-900">
                                                {{ $row->video_sessions_count }}
                                            </div>
                                        </div>
                                        <div class="rounded-lg border p-2.5">
                                            <div class="text-[11px] text-slate-500">Duration</div>
                                            <div class="text-base font-semibold text-slate-900">
                                                {{ $row->material_duration_text ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="rounded-lg border p-2.5">
                                            <div class="text-[11px] text-slate-500">Completion</div>
                                            <div class="text-base font-semibold text-slate-900">
                                                {{ $row->completion_rate }}%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a
                                                href="{{ route('mentor.materials.index', ['topicFilter' => $row->id]) }}"
                                                class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-medium">
                                                Materials
                                            </a>

                                            <a
                                                href="{{ route('mentor.sessions.index', ['topicFilter' => $row->id]) }}"
                                                class="px-3 py-1.5 rounded-lg border text-xs">
                                                Sessions
                                            </a>

                                            <a
                                                href="{{ route('mentor.assessments.index', ['courseFilter' => $row->course_id]) }}"
                                                class="px-3 py-1.5 rounded-lg border text-xs">
                                                Assessments
                                            </a>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button
                                                wire:click.stop="edit('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                                title="Edit">
                                                <span class="sr-only">Edit</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                                                </svg>
                                            </button>

                                            <button
                                                wire:click.stop="delete('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition"
                                                title="Delete">
                                                <span class="sr-only">Delete</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
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