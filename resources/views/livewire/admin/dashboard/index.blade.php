@php
    $attendanceData = [
        'present' => $attendance['present'],
        'late' => $attendance['late'],
        'absent' => $attendance['absent'],
    ];
@endphp

@php
    $calendarSessions = $upcomingSessions->map(function ($s) {
        return [
            'date' => $s->start_at->format('Y-m-d'),
            'title' => $s->title,
        ];
    });
@endphp

<div class="space-y-6">

    <x-ui.page-header
        title="Admin Dashboard"
        subtitle="Operational insights, sessions, and attendance monitoring."
    />

    {{-- FILTER --}}
    <div class="flex gap-2">
        @foreach([1,2,3,4] as $w)
            <button
                wire:click="setWeeks({{ $w }})"
                class="px-4 py-2 rounded-xl text-sm border
                    {{ $weeks === $w ? 'bg-slate-900 text-white' : 'bg-white' }}">
                {{ $w }} Week{{ $w > 1 ? 's' : '' }}
            </button>
        @endforeach
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Users</div>
            <div class="text-3xl font-bold mt-2">{{ number_format($usersCount, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-2">User yang telah mendaftar</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Courses</div>
            <div class="text-3xl font-bold mt-2">{{ number_format($coursesCount, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-2">Pelajaran tersedia</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Topics</div>
            <div class="text-3xl font-bold mt-2">{{ number_format($topicsCount, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-2">Topik yang dapat dipelajari</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Certificates</div>
            <div class="text-3xl font-bold mt-2">{{ number_format($certificatesCount, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-2">Sertifikat yang telah dibagikan</div>
        </div>
    </div>

    {{-- ATTENDANCE --}}
    <section class="bg-white border rounded-2xl p-5 space-y-4">
        <div>
            <h2 class="font-semibold text-lg">Attendance (Last {{ $weeks }} Week{{ $weeks > 1 ? 's' : '' }})</h2>
            <p class="text-sm text-slate-500">
                Total records: {{ number_format($attendance['total'], 0, ',', '.') }}
            </p>
        </div>

        <div id="attendance-chart-data"
             class="hidden"
             data-attendance='@json($attendanceData)'>
        </div>

        <div class="rounded-2xl border p-4" wire:ignore>
            <div class="text-sm font-medium text-slate-700 mb-3">Distribusi Kehadiran</div>
            <div class="h-72">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-emerald-50 border">
                <div class="text-sm text-emerald-600">Present</div>
                <div class="text-2xl font-bold">
                    {{ number_format($attendance['present'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-emerald-600">
                    {{ $attendance['present_pct'] }}%
                </div>
            </div>

            <div class="p-4 rounded-xl bg-yellow-50 border">
                <div class="text-sm text-yellow-600">Late</div>
                <div class="text-2xl font-bold">
                    {{ number_format($attendance['late'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-yellow-600">
                    {{ $attendance['late_pct'] }}%
                </div>
            </div>

            <div class="p-4 rounded-xl bg-red-50 border">
                <div class="text-sm text-red-600">Absent</div>
                <div class="text-2xl font-bold">
                    {{ number_format($attendance['absent'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-red-600">
                    {{ $attendance['absent_pct'] }}%
                </div>
            </div>
        </div>
    </section>

    {{-- PROBLEM SESSIONS --}}
    <section class="bg-white border rounded-2xl p-5 space-y-4">
        <h2 class="font-semibold text-lg">⚠ Problematic Sessions</h2>

        @forelse($problemSessions as $session)
            <div class="border rounded-xl p-4 flex justify-between">
                <div>
                    <div class="font-medium">{{ $session->title }}</div>
                    <div class="text-xs text-slate-500">
                        {{ $session->topic?->name }} • {{ $session->topic?->course?->title }}
                    </div>
                </div>

                <div class="text-sm text-red-600 font-semibold">
                    {{ number_format($session->absent_count) }} absent
                </div>
            </div>
        @empty
            <div class="text-sm text-slate-500">
                No problematic sessions found
            </div>
        @endforelse
    </section>

    {{-- SESSIONS --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- RECENT --}}
        <section class="bg-white border rounded-2xl p-5 space-y-4">
            <h2 class="font-semibold text-lg">Recent Sessions</h2>

            @forelse($recentSessions as $session)
                <div class="border rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <div class="font-medium">{{ $session->title }}</div>

                        <div class="text-xs text-slate-500">
                            {{ $session->topic?->name ?? 'No Topic' }}
                            •
                            {{ $session->topic?->course?->title ?? 'No Course' }}
                        </div>

                        <div class="text-xs text-slate-400 mt-1">
                            {{ $session->start_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-xs whitespace-nowrap self-start">
                        {{ ucfirst($session->status) }}
                    </span>
                </div>
            @empty
                <div class="text-sm text-slate-500">
                    No recent sessions
                </div>
            @endforelse
        </section>

        {{-- UPCOMING --}}
        <section class="bg-white border rounded-2xl p-5 space-y-4">
            <h2 class="font-semibold text-lg">Upcoming Sessions Calendar</h2>

            <div 
                x-data="calendarComponent(@js($calendarSessions))"
                x-init="init()"
                class="space-y-4"
            >
                {{-- HEADER --}}
                <div class="flex justify-between items-center">
                    <button @click="prevMonth()" class="px-3 py-1 border rounded">←</button>
                    <div class="font-medium" x-text="monthYear"></div>
                    <button @click="nextMonth()" class="px-3 py-1 border rounded">→</button>
                </div>

                {{-- DAYS --}}
                <div class="grid grid-cols-7 text-xs text-center text-slate-500">
                    <template x-for="day in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']">
                        <div x-text="day"></div>
                    </template>
                </div>

                {{-- CALENDAR --}}
                <div class="grid grid-cols-7 gap-2 text-sm">
                    <template x-for="blank in blanks">
                        <div></div>
                    </template>

                    <template x-for="day in days">
                        <div class="h-20 border rounded-lg p-1 relative">
                            <div x-text="day" class="text-xs"></div>

                            <!-- DOT -->
                            <template x-if="hasSession(day)">
                                <div class="absolute bottom-1 left-1/2 -translate-x-1/2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </section>

    </div>

</div>

@push('scripts')
    <script>
        function calendarComponent(sessions) {
            return {
                current: new Date(),
                sessions,

                days: [],
                blanks: [],
                monthYear: '',

                init() {
                    this.generate();
                },

                generate() {
                    const year = this.current.getFullYear();
                    const month = this.current.getMonth();

                    const firstDay = new Date(year, month, 1).getDay();
                    const totalDays = new Date(year, month + 1, 0).getDate();

                    this.blanks = Array.from({ length: firstDay });
                    this.days = Array.from({ length: totalDays }, (_, i) => i + 1);

                    this.monthYear = this.current.toLocaleString('default', {
                        month: 'long',
                        year: 'numeric'
                    });
                },

                hasSession(day) {
                    const dateStr = this.formatDate(day);
                    return this.sessions.some(s => s.date === dateStr);
                },

                formatDate(day) {
                    const y = this.current.getFullYear();
                    const m = String(this.current.getMonth() + 1).padStart(2, '0');
                    const d = String(day).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                },

                prevMonth() {
                    this.current.setMonth(this.current.getMonth() - 1);
                    this.generate();
                },

                nextMonth() {
                    this.current.setMonth(this.current.getMonth() + 1);
                    this.generate();
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            let attendanceChartInstance = null;

            function getAttendanceData() {
                const el = document.getElementById('attendance-chart-data');
                if (!el) {
                    return { present: 0, late: 0, absent: 0 };
                }

                try {
                    return JSON.parse(el.dataset.attendance || '{}');
                } catch (e) {
                    return { present: 0, late: 0, absent: 0 };
                }
            }

            function renderAttendanceChart() {
                const canvas = document.getElementById('attendanceChart');
                if (!canvas || typeof Chart === 'undefined') {
                    return;
                }

                const data = getAttendanceData();

                if (attendanceChartInstance) {
                    attendanceChartInstance.data.datasets[0].data = [
                        data.present ?? 0,
                        data.late ?? 0,
                        data.absent ?? 0,
                    ];
                    attendanceChartInstance.update();
                    return;
                }

                attendanceChartInstance = new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Late', 'Absent'],
                        datasets: [{
                            data: [data.present ?? 0, data.late ?? 0, data.absent ?? 0],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            if (!window.__attendanceChartBound) {
                window.__attendanceChartBound = true;

                document.addEventListener('livewire:load', () => {
                    renderAttendanceChart();

                    if (window.Livewire && typeof window.Livewire.hook === 'function') {
                        window.Livewire.hook('message.processed', () => {
                            renderAttendanceChart();
                        });
                    }
                });
            }
        })();
    </script>
@endpush