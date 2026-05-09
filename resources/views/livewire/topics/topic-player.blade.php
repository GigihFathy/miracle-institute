@php
    use Carbon\Carbon;
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-6 lg:px-36 pb-10">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 space-y-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3 min-w-0">
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    {{ $topic->course?->title }}
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold">{{ $topic->name }}</h1>
                    <p class="text-slate-600 max-w-3xl leading-7">{{ $topic->description }}</p>
                </div>

                @if(session('active_role') === 'disciples' && auth()->user()?->can('manage_topics'))
                    <a href="{{ route('mentor.materials.index', $topic->slug) }}"
                       class="inline-flex px-4 py-2 rounded-xl border text-sm">
                        Open Mentor Workspace
                    </a>
                @endif
            </div>

            <div class="flex flex-col gap-2 items-start lg:items-end shrink-0">

                @if($topicCompleted)
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                        TOPIC COMPLETED
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ strtoupper($topicStatus ?? 'not_started') }}
                    </span>
                @endif
                

            </div>
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

        <div class="flex flex-wrap gap-2 justify-between items-center">
            <div>
                <button wire:click="setTab('materials')" class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'materials' ? 'bg-slate-900 text-white' : 'bg-white' }}">Materials</button>
                <button wire:click="setTab('sessions')" class="px-4 py-2 rounded-xl border transition {{ $activeTab === 'sessions' ? 'bg-slate-900 text-white' : 'bg-white' }}">Sessions</button>
            </div>

            @if (!$topicCompleted)
                @if($activeMaterial)
                    @php
                        $isLocked = !$hasSessionEnded; 
                    @endphp

                    <button wire:click="{{ $isLocked ? '' : 'markViewed' }}"
                            {{ $isLocked ? 'disabled' : '' }}
                            class="group relative px-6 py-2 rounded-xl font-semibold border transition-all duration-300 flex items-center gap-2
                            {{ $isLocked 
                                ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-80' 
                                : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 hover:shadow-emerald-200/50 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0' 
                            }}">
                        
                        @if($isLocked)
                            <svg xmlns="http://w3.org" class="h-4 w-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Attend session to complete</span>
                        @else
                            <svg xmlns="http://w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Mark topic as completed</span>
                        @endif

                        @if($isLocked)
                            <span class="absolute -top-10 left-1/2 -translate-x-1/2 scale-0 transition-all rounded bg-gray-800 p-2 text-xs text-white group-hover:scale-100 whitespace-nowrap">
                                Tombol akan aktif setelah sesi berakhir
                            </span>
                        @endif
                    </button>
                @endif
            @endif
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

                @forelse($topic->materials as $material)
                    @if($loop->first)
                        <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">
                    @endif

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
                    </button>

                    @if($loop->last)
                        </div>
                    @endif
                @empty
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Materi sedang dipersiapkan</div>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Mentor belum mengunggah materi untuk topik ini. Tab ini tetap aktif agar mahasiswa bisa memantau pembaruan kapan saja.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $activeMaterial?->name ?? 'Select a material' }}</h2>
                        <p class="text-sm text-slate-500">Type: {{ $activeMaterial?->type ?? '-' }}</p>
                    </div>
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
                @elseif($hasMaterials)
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Pilih materi untuk melihat detail</div>
                        <p class="text-sm text-slate-600 mt-1">
                            Klik salah satu card materi di atas untuk membuka preview atau file terkait.
                        </p>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                        <div class="font-semibold text-slate-900">Belum ada materi tersedia</div>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Mentor belum menyiapkan materi untuk topik ini. Silakan kembali nanti atau cek tab Sessions untuk informasi jadwal belajar.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if($activeTab === 'sessions')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Sessions</h2>
                        <p class="text-sm text-slate-500">
                            Pastikan kamu memeriksa status waktu di setiap sesi.
                        </p>
                    </div>

                    @php
                        $now = Carbon::now();
                        $session = $topic->videoSessions->first();

                        $open = null;
                        $close = null;
                        $isOpen = false;
                        $isPast = false;

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
                @php
                    $attendance = $sessionAttendances->get($session->id);
                @endphp

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
                            <div class="text-xs text-slate-500">Attendance Status</div>
                            <div class="font-semibold mt-1">
                                {{ $attendance?->status ?? 'absent' }}
                            </div>
                        </div>

                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Check In</div>
                            <div class="font-semibold mt-1">
                                {{ $attendance?->check_in_at?->format('d M Y, H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>

                     @livewire('sessions.attendance-button', ['sessionId' => $session->id], key('attendance-'.$session->id))
                </div>
            @empty
                <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                    <div class="font-semibold text-slate-900">Belum ada sesi terjadwal</div>
                    <p class="text-sm text-slate-600 mt-1 leading-6">
                        Mentor belum menjadwalkan sesi untuk topik ini. Tab tetap tersedia agar mahasiswa bisa memantau jadwal begitu dipublikasikan.
                    </p>
                </div>
            @endforelse
        </section>
    @endif
</div>