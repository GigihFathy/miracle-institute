@php
    $isMentor = session('active_role') === 'disciples';
@endphp

<div class="space-y-6 lg:px-36">

    <!-- HEADER -->
    <section class="rounded-3xl bg-white border overflow-hidden">
        <div class="grid xl:grid-cols-2">

            <!-- LEFT -->
            <div class="p-8 space-y-5">

                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-400 uppercase">
                        {{ $course->studyProgram?->title }}
                    </div>

                    <!-- ROLE BASED ACTION -->
                    @if($isMentor)
                        <div class="flex gap-2">
                            <a href="{{ route('mentor.topics.index') }}"
                               class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                                Manage Topics
                            </a>
                        </div>
                    @else
                        @if($courseCertificate)
                            <a href="{{ route('certificates.download', $courseCertificate->id) }}"
                               class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm">
                                View Certificate
                            </a>
                        @else
                            <a href="{{ route('certificates.course.claim', $course->id) }}"
                               class="px-4 py-2 border rounded-xl text-sm">
                                Claim Certificate
                            </a>
                        @endif
                    @endif
                </div>

                <h1 class="text-3xl font-bold">{{ $course->title }}</h1>

                <p class="text-slate-600">{{ $course->description }}</p>

                <!-- STATS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Topics</div>
                        <div class="font-semibold">{{ $course->topics->count() }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Credit</div>
                        <div class="font-semibold">{{ $course->credit }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Quota</div>
                        <div class="font-semibold">{{ $course->quota }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Status</div>
                        <div class="font-semibold">{{ ucfirst($course->status) }}</div>
                    </div>
                </div>

                <!-- ACTION -->
                <div class="flex gap-3">
                    @if(!$isMentor)
                        @if(!auth()->check())
                            <a href="{{ route('login') }}"
                            class="px-5 py-3 bg-slate-900 text-white rounded-xl text-sm">
                                Login to enroll
                            </a>
                        @else
                            @if($enrolled)
                                <span class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm">
                                    Enrolled
                                </span>
                            @else
                                <button wire:click="enroll"
                                        class="px-5 py-3 bg-slate-900 text-white rounded-xl text-sm">
                                    Enroll
                                </button>
                            @endif
                        @endif
                    @else
                        <span class="px-4 py-2 bg-slate-100 rounded-xl text-sm">
                            Mentor Mode
                        </span>
                    @endif
                </div>
            </div>

            <!-- IMAGE -->
            <div class="bg-slate-100">
                <img src="{{ asset('images/dummyPNG.png') }}"
                     class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- TOPICS -->
    <section class="space-y-4">
        <h2 class="text-xl font-semibold">Topics</h2>

        <div class="grid md:grid-cols-2 gap-4">
            @foreach($course->topics as $topic)

                @php
                    $status = $topicStatusMap[$topic->id] ?? 'not_started';
                    $percent = $status === 'completed' ? 100 : ($status === 'in_progress' ? 50 : 0);
                    $certificate = $topicCertificates[$topic->id] ?? null;
                @endphp

                <div class="border p-5 rounded-2xl bg-white space-y-4">

                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $topic->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $topic->description }}</p>
                        </div>

                        <span class="text-xs bg-slate-100 px-2 py-1 rounded">
                            {{ strtoupper($status) }}
                        </span>
                    </div>

                    <div class="h-2 bg-slate-100 rounded">
                        <div class="bg-slate-900 h-2 rounded"
                             style="width: {{ $percent }}%">
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('topics.show', $topic->slug) }}"
                           class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                            Open
                        </a>

                        @if($isMentor)
                            <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                               class="px-4 py-2 border rounded-xl text-sm">
                                Manage
                            </a>
                        @else
                            @if($certificate)
                                <a href="{{ route('certificates.download', $certificate['id']) }}"
                                   class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm">
                                    Certificate
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>