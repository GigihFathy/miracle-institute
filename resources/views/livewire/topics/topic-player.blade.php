@php
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-6 lg:px-36">
    <section class="rounded-3xl bg-white border p-6 sm:p-8 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    {{ $topic->course?->title }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold mt-2">{{ $topic->name }}</h1>
                <p class="text-slate-600 mt-3 max-w-3xl">{{ $topic->description }}</p>

                @if(session('active_role') === 'disciples' && auth()->user()->can('manage_topics'))
                    <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                    class="inline-flex px-4 py-2 rounded-xl border text-sm">
                        Open Mentor Workspace
                    </a>
                @endif
            </div>

            @if($topicCertificate)
                <a href="{{ route('certificates.download', $topicCertificate->id) }}"
                   class="inline-flex px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-medium">
                    View Certificate
                </a>
            @elseif($topicStatus === 'completed')
                <a href="{{ route('certificates.topic.claim', $topic->id) }}"
                   class="inline-flex px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium">
                    Claim Certificate
                </a>
            @else
                <span class="inline-flex px-4 py-2 rounded-xl border border-slate-200 text-slate-400 text-sm">
                    Certificate Pending
                </span>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <button wire:click="setTab('materials')"
                    class="px-4 py-2 rounded-xl border {{ $activeTab === 'materials' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Materials
            </button>
            <button wire:click="setTab('sessions')"
                    class="px-4 py-2 rounded-xl border {{ $activeTab === 'sessions' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Sessions
            </button>
            <button wire:click="setTab('assessment')"
                    class="px-4 py-2 rounded-xl border {{ $activeTab === 'assessment' ? 'bg-slate-900 text-white' : 'bg-white' }}">
                Assessment
            </button>
        </div>
    </section>

    @if($activeTab === 'materials')
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-5">
                <div class="flex items-end justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Material Library</h2>
                        <p class="text-sm text-slate-500">Geser ke kanan untuk melihat seluruh materi.</p>
                    </div>
                </div>

                <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">
                    @foreach($topic->materials as $material)
                        <button wire:click="selectMaterial('{{ $material->id }}')"
                                class="shrink-0 w-[280px] text-left rounded-2xl border p-5 transition snap-start
                                {{ $activeMaterial?->id === $material->id ? 'bg-slate-900 text-white border-slate-900' : 'bg-white hover:border-slate-400' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-semibold">{{ $material->name }}</div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $activeMaterial?->id === $material->id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-600' }}">
                                    {{ strtoupper($material->type) }}
                                </span>
                            </div>

                            <p class="text-sm mt-3 {{ $activeMaterial?->id === $material->id ? 'text-slate-300' : 'text-slate-500' }}">
                                {{ \Illuminate\Support\Str::limit($material->path ?: $material->external_url ?: 'No preview available', 90) }}
                            </p>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $activeMaterial?->name }}</h2>
                        <p class="text-sm text-slate-500">Type: {{ $activeMaterial?->type }}</p>
                    </div>

                    @if($activeMaterial)
                        <button wire:click="markViewed"
                                class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                            Mark viewed
                        </button>
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
                @else
                    <x-ui.empty-state
                        title="No material selected"
                        description="Pilih material dari daftar di atas."
                    />
                @endif
            </div>
        </section>
    @endif

    @if($activeTab === 'sessions')
        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-semibold">Sessions</h2>
                <p class="text-sm text-slate-500">Sesi ditampilkan dalam blok vertikal agar nyaman dibaca.</p>
            </div>

            @forelse($topic->sessions as $session)
                <div class="rounded-2xl bg-white border p-5 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $session->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                {{ $session->start_at->format('d M Y, H:i') }} - {{ $session->end_at->format('H:i') }}
                            </p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $session->status }}</span>
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

    @if($activeTab === 'assessment')
        <section class="space-y-5">
            <div class="rounded-2xl bg-white border p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Assessment</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $assessmentMeta['title'] ?? ($activeAssessment?->title ?? 'No assessment available yet.') }}
                        </p>
                    </div>

                    @if($activeAssessment)
                        @if($this->activeAttempt)
                            <a href="{{ route('assessments.take', $activeAssessment->id) }}"
                            class="px-4 py-2 bg-yellow-500 text-white rounded-xl">
                                Resume Test
                            </a>
                        @else
                            <a href="{{ route('assessments.take', $activeAssessment->id) }}"
                            class="px-4 py-2 bg-black text-white rounded-xl">
                                Start Test
                            </a>
                        @endif
                    @endif
                </div>

                @if($assessmentMeta)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Time Limit</div>
                            <div class="font-semibold mt-1">
                                {{ $assessmentMeta['time_limit_minutes'] ? $assessmentMeta['time_limit_minutes'] . ' minutes' : 'No limit' }}
                            </div>
                        </div>
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Start Date</div>
                            <div class="font-semibold mt-1">{{ $assessmentMeta['start_date'] }}</div>
                        </div>
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Estimated Completion</div>
                            <div class="font-semibold mt-1">{{ $assessmentMeta['estimated_minutes'] }} minutes</div>
                        </div>
                        <div class="rounded-xl border p-4 bg-slate-50">
                            <div class="text-xs text-slate-500">Passing Grade</div>
                            <div class="font-semibold mt-1">{{ $assessmentMeta['passing_grade'] }}</div>
                        </div>
                    </div>

                    <div class="rounded-2xl border p-5 bg-slate-50">
                        <div class="text-sm font-semibold mb-2">Instructions</div>
                        <ul class="space-y-2 text-sm text-slate-600 list-disc pl-5">
                            @foreach($assessmentMeta['instructions'] as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>