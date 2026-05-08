<div class="space-y-6 lg:px-5 2xl:px-8 pb-10 scale-[0.90] origin-top">

    {{-- HERO --}}
    <section class="rounded-[28px] border bg-white shadow-sm overflow-hidden">
        <div>

            <div class="p-7 lg:p-10 space-y-6">

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                    <div class="space-y-3 max-w-3xl">
                        <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                            Disciples Studio
                        </div>

                        <div class="space-y-2">
                            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                                Learning Management Studio
                            </h1>

                            <p class="text-sm lg:text-[15px] leading-7 text-slate-600">
                                Workspace operasional untuk mengelola course, memantau performa pembelajaran,
                                dan mengontrol distribusi materi secara terstruktur.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                            + New Course
                        </button>
                    </div>
                </div>

                {{-- STATS --}}
                <div class="grid grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 gap-4">

                    @foreach($statsCards as $card)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
                            <div class="text-xs text-slate-500">
                                {{ $card['label'] }}
                            </div>

                            <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                {{ $card['value'] }}
                            </div>

                            <div class="mt-1 text-xs leading-5 text-slate-500">
                                {{ $card['note'] }}
                            </div>
                        </div>
                    @endforeach

                </div>

                
            </div>
        </div>
    </section>
    
    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">

            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search course..."
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900"
            >

            <select
                wire:model.live="studyProgramFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">

                <option value="">All programs</option>

                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->id }}">
                        {{ $sp->title }}
                    </option>
                @endforeach
            </select>

            <select
                wire:model.live="statusFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">

                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <select
                wire:model.live="perPage"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">

                <option value="12">12 / page</option>
                <option value="24">24 / page</option>
                <option value="48">48 / page</option>
            </select>

        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">

        {{-- COURSE GRID --}}
        <div class="space-y-5 min-w-0">

            @if($rows->isEmpty())

                <x-ui.empty-state
                    title="No course found"
                    description="Tidak ada course yang cocok dengan filter saat ini."
                />

            @else

                <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-5">

                    @foreach($rows as $row)

                        <div
                            wire:key="course-{{ $row->id }}"
                            class="group rounded-[26px] border bg-white shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">

                            <button
                                type="button"
                                wire:click="selectCourse('{{ $row->id }}')"
                                class="w-full text-left">

                                {{-- IMAGE --}}
                                <div class="relative h-48 overflow-hidden bg-slate-100">

                                    <img
                                        src="{{ $row->poster ? asset($row->poster) : asset('images/test.png') }}"
                                        alt="{{ $row->title }}"
                                        class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                                    <div class="absolute inset-x-5 bottom-5 flex items-end justify-between gap-4">

                                        <div class="min-w-0">
                                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-200 truncate">
                                                {{ $row->studyProgram?->title }}
                                            </div>

                                            <h3 class="mt-1 text-xl font-semibold text-white leading-tight line-clamp-2">
                                                {{ $row->title }}
                                            </h3>
                                        </div>

                                        <span class="shrink-0 rounded-full bg-white/15 backdrop-blur px-3 py-1 text-[11px] text-white border border-white/15">
                                            {{ ucfirst($row->status) }}
                                        </span>

                                    </div>
                                </div>

                                {{-- BODY --}}
                                <div class="p-5 space-y-5">

                                    <div class="min-h-[4.5rem]"> 
                                        <p class="text-sm text-slate-600 leading-6 line-clamp-3">
                                            {{ $row->description }}
                                        </p>
                                    </div>

                                    {{-- METRICS --}}
                                    <div class="grid grid-cols-4 gap-2">

                                        <div class="rounded-xl bg-slate-50 border p-3">
                                            <div class="text-[11px] text-slate-500">
                                                Topics
                                            </div>

                                            <div class="mt-1 text-lg font-bold text-slate-900">
                                                {{ $row->topics_count }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-slate-50 border p-3">
                                            <div class="text-[11px] text-slate-500">
                                                Enroll
                                            </div>

                                            <div class="mt-1 text-lg font-bold text-slate-900">
                                                {{ $row->enrollments_count }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-slate-50 border p-3">
                                            <div class="text-[11px] text-slate-500">
                                                Cert
                                            </div>

                                            <div class="mt-1 text-lg font-bold text-slate-900">
                                                {{ $row->certificates_count }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-slate-50 border p-3">
                                            <div class="text-[11px] text-slate-500">
                                                Complete
                                            </div>

                                            <div class="mt-1 text-lg font-bold text-slate-900">
                                                {{ $row->completion_rate }}%
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </button>

                            {{-- FOOTER --}}
                            <div class="border-t bg-slate-50/70 px-5 py-4 space-y-3">

                                <div class="flex flex-wrap gap-2">

                                    <a
                                        href="{{ route('mentor.topics.index', ['courseFilter' => $row->id]) }}"
                                        class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-medium">
                                        Topics
                                    </a>

                                    <a
                                        href="{{ route('mentor.materials.index', ['courseFilter' => $row->id]) }}"
                                        class="px-3 py-2 rounded-xl border text-xs">
                                        Materials
                                    </a>

                                    <a
                                        href="{{ route('mentor.sessions.index', ['courseFilter' => $row->id]) }}"
                                        class="px-3 py-2 rounded-xl border text-xs">
                                        Sessions
                                    </a>

                                </div>

                                <div class="flex items-center justify-between">

                                    <div class="text-xs text-slate-500">
                                        {{ $row->assessment_label }}
                                    </div>

                                    <div class="flex gap-2">

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
        <aside class="sticky top-24 space-y-4">

            <div class="rounded-[28px] border bg-white shadow-sm overflow-hidden">

                @if($selectedCourse)

                    <div class="p-6 space-y-5">

                        <div class="space-y-2">
                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                                Selected Course
                            </div>

                            <h2 class="text-2xl font-bold tracking-tight text-slate-900 leading-tight">
                                {{ $selectedCourse->title }}
                            </h2>
                        </div>

                        <div class="aspect-video rounded-2xl overflow-hidden border bg-slate-100">
                            <img
                                src="{{ $selectedCourse->poster ? asset($selectedCourse->poster) : asset('images/test.png') }}"
                                alt="{{ $selectedCourse->title }}"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">
                                    Topics
                                </div>

                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedCourse->topics_count }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">
                                    Completion
                                </div>

                                <div class="mt-1 text-xl font-bold text-slate-900">
                                    {{ $selectedCourse->completion_rate }}%
                                </div>
                            </div>

                        </div>

                        <div class="space-y-3">

                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div
                                    class="h-2 rounded-full bg-slate-900"
                                    style="width: {{ $selectedCourse->completion_rate }}%">
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span>Learning completion</span>
                                <span>{{ $selectedCourse->completion_rate }}%</span>
                            </div>

                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">
                                Description
                            </div>

                            <p class="mt-2 text-sm leading-7 text-slate-600">
                                {{ $selectedCourse->description }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2">

                            <a
                                href="{{ route('mentor.topics.index', ['courseFilter' => $selectedCourse->id]) }}"
                                class="h-11 rounded-xl bg-slate-900 text-white text-xs font-medium flex items-center justify-center">
                                Manage Topics
                            </a>

                            <a
                                href="{{ route('mentor.materials.index', ['courseFilter' => $selectedCourse->id]) }}"
                                class="h-11 rounded-xl border text-xs font-medium flex items-center justify-center">
                                Materials
                            </a>

                            <a
                                href="{{ route('mentor.sessions.index', ['courseFilter' => $selectedCourse->id]) }}"
                                class="h-11 rounded-xl border text-xs font-medium flex items-center justify-center">
                                Sessions
                            </a>

                            <a
                                href="{{ route('mentor.assessments.index', ['courseFilter' => $selectedCourse->id]) }}"
                                class="h-11 rounded-xl border text-xs font-medium flex items-center justify-center">
                                Assessments
                            </a>

                        </div>

                    </div>

                @else

                    <div class="p-10">
                        <x-ui.empty-state
                            title="Select a course"
                            description="Klik salah satu course untuk membuka detail operasional."
                        />
                    </div>

                @endif

            </div>

        </aside>

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

