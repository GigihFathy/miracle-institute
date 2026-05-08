<div class="space-y-8 px-4 sm:px-6 lg:px-36">

    {{-- HERO --}}
    <section class="rounded-2xl border bg-white shadow-sm overflow-hidden">
        <div>

            <div class="p-6 lg:p-8 space-y-6">

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                    <div class="space-y-2 max-w-3xl">
                        <div class="text-[11px] uppercase tracking-[0.3em] text-slate-400">
                            Disciples Studio
                        </div>

                        <div class="space-y-1">
                            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">
                                Course Management
                            </h1>

                            <p class="text-sm text-slate-600">
                                Kelola daftar course dan buka detail saat dibutuhkan.
                            </p>
                        </div>
                    </div>
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
                    placeholder="Search course..."
                    class="h-10 w-full rounded-xl border-slate-200 pl-9 pr-3 text-sm focus:border-slate-900 focus:ring-slate-900"
                >
            </div>

            <div class="flex flex-col sm:flex-row gap-3 lg:w-auto">
                <select
                    wire:model.live="statusFilter"
                    class="h-10 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                    <option value="">All status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <select
                    wire:model.live="perPage"
                    class="h-10 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                    <option value="12">12 / page</option>
                    <option value="24">24 / page</option>
                    <option value="48">48 / page</option>
                </select>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <section>
        <div class="min-w-0">

            @if($rows->isEmpty())

                <x-ui.empty-state
                    title="No course found"
                    description="Tidak ada course yang cocok dengan filter saat ini."
                />

            @else

                <div class="rounded-2xl bg-white border overflow-hidden divide-y divide-slate-100">

                    @foreach($rows as $row)

                        <div
                            wire:key="course-{{ $row->id }}"
                            x-data="{ open: false }"
                            class="group">

                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full text-left px-4 py-4 hover:bg-slate-50/70 transition">
                                <div class="flex items-start gap-4">
                                    <img
                                        src="{{ $row->poster ? asset($row->poster) : asset('images/test.png') }}"
                                        alt="{{ $row->title }}"
                                        class="h-16 w-24 rounded-lg object-cover border border-slate-200 shrink-0">

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                {{ $row->title }}
                                            </h3>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $row->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $row->studyProgram?->title }} - {{ $row->assessment_label }}
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
                                    <div class="space-y-2">
                                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Topics in this course
                                        </div>

                                        @forelse($row->topics as $topic)
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2.5">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-slate-900 truncate">
                                                        {{ $topic->name }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">
                                                        {{ ucfirst($topic->status) }} - {{ $topic->materials_count }} materials
                                                    </div>
                                                </div>

                                                <a
                                                    href="{{ route('mentor.materials.index', ['topicFilter' => $topic->id]) }}"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-medium whitespace-nowrap">
                                                    Materials
                                                </a>
                                            </div>
                                        @empty
                                            <div class="rounded-lg border border-dashed border-slate-300 px-3 py-3 text-xs text-slate-500">
                                                No topics in this course yet.
                                            </div>
                                        @endforelse
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
        :title="$editingId ? 'Edit Course' : 'New Course'"
        description="Studio form untuk pengelolaan course."
        maxWidth="max-w-3xl"
    >
        <div class="space-y-4">
            <select wire:model="study_program_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select program</option>
                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->title }}</option>
                @endforeach
            </select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="title" class="w-full border rounded-xl px-4 py-3" placeholder="Title">
                <input wire:model="slug" class="w-full border rounded-xl px-4 py-3 bg-slate-50" placeholder="Slug">
            </div>

            <input wire:model="poster" class="w-full border rounded-xl px-4 py-3" placeholder="Poster path/url">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="credit" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Credit">
                <input wire:model="quota" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Quota">
            </div>

            <textarea wire:model="description" rows="5" class="w-full border rounded-xl px-4 py-3" placeholder="Description"></textarea>

            <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
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

