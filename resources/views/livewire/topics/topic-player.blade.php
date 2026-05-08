@php
    use Carbon\Carbon;
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-6 lg:px-36 pb-10">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 space-y-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-3">
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    {{ $topic->course?->title }}
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold">{{ $topic->name }}</h1>
                    <p class="text-slate-600 max-w-3xl leading-7">{{ $topic->description }}</p>
                </div>

                @if(session('active_role') === 'disciples' && auth()->user()->can('manage_topics'))
                    <a href="{{ route('mentor.materials.index', $topic->slug) }}"
                       class="inline-flex px-4 py-2 rounded-xl border text-sm">
                        Open Mentor Workspace
                    </x-ui.button>
                @endif
            </div>

            <span class="px-3 py-1 rounded-full text-xs bg-slate-100 whitespace-nowrap">
                {{ strtoupper($topicStatus ?? 'not_started') }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Materials</div>
                <div class="text-2xl font-bold mt-1">{{ $topic->materials->count() }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Sessions</div>
                <div class="text-2xl font-bold mt-1">{{ $topic->videoSessions->count() }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Attendance Records</div>
                <div class="text-2xl font-bold mt-1">{{ $attendanceStats['checked_in'] }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Progress</div>
                <div class="text-2xl font-bold mt-1">
                    {{ strtoupper($topicStatus ?? 'not_started') }}
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button wire:click="setTab('materials')"
                    class="px-4 py-2 rounded-xl border {{ $activeTab === 'materials' ? 'bg-primary text-white' : 'bg-white' }}">
                Materials
            </button>

            <button wire:click="setTab('sessions')"
                    class="px-4 py-2 rounded-xl border {{ $activeTab === 'sessions' ? 'bg-primary text-white' : 'bg-white' }}">
                Sessions
            </button>
        </div>
    </section>

    @if($activeTab === 'materials')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">Material Library</h2>
                        <p class="text-sm text-slate-500">
                            Semua materi dalam topik ini dikumpulkan di satu tempat.
                        </p>
                    </div>
                </div>

                @if($topic->materials->isEmpty())
                    <x-ui.empty-state
                        title="No materials"
                        description="Belum ada material yang ditambahkan untuk topic ini."
                    />
                @else
                    <div class="flex gap-4 overflow-hidden pb-2 snap-x snap-mandatory">
                        @foreach($topic->materials as $material)
                            <button wire:click="selectMaterial('{{ $material->id }}')"
                                    class="shrink-0 w-[280px] text-left rounded-2xl border p-5 transition snap-start
                                    {{ $activeMaterial?->id === $material->id ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:border-slate-400' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-semibold">{{ $material->name }}</div>
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $activeMaterial?->id === $material->id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ strtoupper($material->type) }}
                                    </span>
                                </div>

                                <p class="text-sm mt-3
                                    {{ $activeMaterial?->id === $material->id ? 'text-slate-300' : 'text-slate-500' }}">
                                    {{ \Illuminate\Support\Str::limit(basename($material->path ?: $material->external_url ?: 'No preview available'), 90) }}
                                </p>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $activeMaterial?->name ?? 'Select a material' }}</h2>
                        <p class="text-sm text-slate-500">Type: {{ $activeMaterial?->type ?? '-' }}</p>
                    </div>

                    @if($activeMaterial)
                        <x-ui.button wire:click="markViewed" variant="primary" size="md">Mark viewed</x-ui.button>
                    @endif
                </div>

                @if($activeMaterial && $materialUrl)
                    @if($activeMaterial->type === 'video')
                        <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100">
                            <iframe src="{{ $materialUrl }}" class="w-full h-full" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="rounded-2xl border p-5 bg-slate-50">
                            <a href="{{ $materialUrl }}" target="_blank" class="text-slate-900 underline">
                                Open / download material
                            </a>
                        </div>
                    @endif
                @elseif($topic->materials->isEmpty())
                    <x-ui.empty-state
                        title="No material available"
                        description="Topic ini belum memiliki material."
                    />
                @else
                    <x-ui.empty-state
                        title="No material selected"
                        description="Pilih material dari daftar di atas untuk melihat detailnya."
                    />
                @endif
            </div>
        </section>
    @endif

    @if($activeTab === 'sessions')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Sessions</h2>
                        <p class="text-sm text-slate-500">
                            Pastikan kamu memeriksa status waktu di setiap sesi.
                        </p>
                    </div>

                    @php
                        $now = Carbon::now();

                        $session = $topic->videoSessions->first();

                        if ($session) {
                            $start = $session->start_at;
                            $end = $session->end_at;

                            $open = $start;
                            $close = $start->copy()->addMinutes(45)->lt($end->copy()->subMinutes(15))
                                ? $start->copy()->addMinutes(45)
                                : $end->copy()->subMinutes(15);

                            $isOpen = $now->between($open, $close);
                            $isPast = $now->gt($close);
                        }
                    @endphp

                    @if($session)
                        <div class="rounded-xl border px-3 py-2 text-xs
                            {{ $isOpen ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                            ($isPast ? 'bg-red-50 text-red-600 border-red-200' :
                            'bg-slate-50 text-slate-600') }}">

                            Clock-in: {{ $open->format('H:i') }} - {{ $close->format('H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            @forelse($topic->videoSessions as $session)
                <div class="rounded-2xl bg-white border p-5 space-y-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $session->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                {{ $session->start_at->format('d M Y, H:i') }} - {{ $session->end_at->format('H:i') }}
                            </p>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Access Window</div>
                            <div class="font-semibold mt-1">
                                {{ $session->start_at->format('H:i') }} - {{ $session->start_at->copy()->addMinutes(45)->lt($session->end_at->copy()->subMinutes(15)) ? $session->start_at->copy()->addMinutes(45)->format('H:i') : $session->end_at->copy()->subMinutes(15)->format('H:i') }}
                            </div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Material/Attendance</div>
                            <div class="font-semibold mt-1">
                                {{ $sessionAttendances[$session->id]->status ?? 'Not checked in' }}
                            </div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Check In</div>
                            <div class="font-semibold mt-1">
                                {{ $sessionAttendances[$session->id]->check_in_at?->format('d M Y, H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    @livewire('sessions.attendance-button', ['sessionId' => $session->id], key('attendance-'.$session->id))
                </div>
            @empty
                <x-ui.empty-state
                    title="No sessions"
                    description="Belum ada sesi untuk topic ini."
                />
            @endforelse
        </section>
    @endif
</div>