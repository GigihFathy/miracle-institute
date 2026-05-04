@php
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-5">
    <x-ui.page-header
        title="{{ $isMentor ? 'Courses (Mentor View)' : 'Course Catalog' }}"
        subtitle="{{ $isMentor ? 'Kelola course yang kamu ajar.' : 'Pencarian dan filter course.' }}"
    />

    <!-- FILTER -->
    <div class="rounded-2xl bg-white border p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="search"
                   wire:model.debounce.300ms="search"
                   placeholder="Cari course..."
                   class="border rounded-xl px-4 py-2">

            <select wire:model="studyProgram" class="border rounded-xl px-3 py-2">
                <option value="">Semua program</option>
                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->slug }}">{{ $sp->title }}</option>
                @endforeach
            </select>

            <select wire:model="sort" class="border rounded-xl px-3 py-2">
                <option value="latest">Newest</option>
                <option value="title">Title</option>
                <option value="topics">Most topics</option>
            </select>

            <select wire:model="perPage" class="border rounded-xl px-3 py-2">
                <option value="9">9 / halaman</option>
                <option value="12">12 / halaman</option>
                <option value="24">24 / halaman</option>
            </select>
        </div>
    </div>

    <!-- LIST -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($courses as $course)
            @php $enrolled = in_array($course->id, $enrolledCourseIds, true); @endphp

            <div class="rounded-2xl bg-white border overflow-hidden flex flex-col">
                <div class="aspect-[16/9] bg-slate-100">
                    <img src="{{ asset('storage/' . $course->poster) }}"
                         class="w-full h-full object-cover">
                </div>

                <div class="p-5 flex flex-col gap-3 flex-1">
                    <div>
                        <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $course->studyProgram?->title }}</p>
                    </div>

                    <div class="text-xs text-slate-500">
                        {{ $course->topics_count }} topics
                    </div>

                    <div class="mt-auto flex justify-between items-center">
                        <a href="{{ route('courses.show', $course->slug) }}"
                           class="text-sm underline">
                            Open
                        </a>

                        @if($isMentor)
                            <a href="{{ route('mentor.topics.index') }}"
                               class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                                Manage
                            </a>
                        @else
                            @if(auth()->check())
                                @if($enrolled)
                                    <span class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs">
                                        Enrolled
                                    </span>
                                @else
                                    <button wire:click="enroll('{{ $course->id }}')"
                                            class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                                        Enroll
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                class="px-4 py-2 border rounded-xl text-sm">
                                    Login to enroll
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <x-ui.empty-state title="Tidak ditemukan" description="Coba filter lain." />
        @endforelse
    </div>

    <div>{{ $courses->links() }}</div>
</div>