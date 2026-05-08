@php
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-5 lg:px-36">
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
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($courses as $course)
            @php $enrolled = in_array($course->id, $enrolledCourseIds, true); @endphp

            <div class="rounded-2xl overflow-hidden flex flex-col group border border-slate-100 bg-white transition p-3">
                <div class="rounded-md overflow-hidden">
                    @if(!empty($course->image))
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-36 object-cover">
                    @else
                        <div class="w-full h-36 flex items-center justify-center bg-slate-200">
                            <svg width="120" height="68" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="280" height="158" fill="#e6e9ee"/></svg>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col gap-3 flex-1 mt-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ $course->studyProgram?->title }}</p>
                        <h3 class="font-semibold text-sm mt-1 leading-tight">{{ \Illuminate\Support\Str::limit($course->title, 70) }}</h3>
                    </div>

                    <div class="text-xs text-slate-500">
                        {{ $course->topics_count }} topics
                    </div>

                    <div class="mt-auto flex justify-between items-center">
                        <x-ui.button href="{{ route('courses.show', $course->slug) }}" variant="primary" size="sm">
                            Open
                        </x-ui.button>

                        @if($isMentor)
                            <x-ui.button href="{{ route('mentor.topics.index') }}" variant="primary" size="sm">
                                Manage
                            </x-ui.button>
                        @else
                            @if(auth()->check())
                                @if($enrolled)
                                    <x-ui.badge variant="success" size="sm">Enrolled</x-ui.badge>
                                @else
                                    <button wire:click="enroll('{{ $course->id }}')"
                                            class="px-4 py-2 bg-primary text-white rounded-xl text-sm">
                                        Enroll
                                    </button>
                                @endif
                            @else
                                <x-ui.button href="{{ route('login') }}" variant="outline" size="sm">Login to enroll</x-ui.button>
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