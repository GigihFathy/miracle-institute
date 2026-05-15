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

@php
    $calendarWeekdays = __('admin.dashboard.calendar.weekdays');
    $calendarMonths = __('admin.dashboard.calendar.months');
@endphp

<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.dashboard.page_title') }}"
        subtitle="{{ __('admin.dashboard.page_subtitle') }}"
    />

    <div class="flex gap-2 flex-wrap">
        @foreach([1,2,3,4] as $w)
            <button
                wire:click="setWeeks({{ $w }})"
                class="rounded-xl border px-4 py-2 text-sm
                    {{ $weeks === $w ? 'bg-slate-900 text-white' : 'bg-white' }}"
            >
                {{ trans_choice('admin.dashboard.filters.weeks', $w, ['count' => $w]) }}
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.dashboard.stats.users.label') }}</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($usersCount, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ __('admin.dashboard.stats.users.hint') }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.dashboard.stats.courses.label') }}</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($coursesCount, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ __('admin.dashboard.stats.courses.hint') }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.dashboard.stats.topics.label') }}</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($topicsCount, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ __('admin.dashboard.stats.topics.hint') }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.dashboard.stats.certificates.label') }}</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($certificatesCount, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ __('admin.dashboard.stats.certificates.hint') }}</div>
        </div>
    </div>

    <section class="space-y-4 rounded-2xl border bg-white p-5">
        <div>
            <h2 class="text-lg font-semibold">
                {{ trans_choice('admin.dashboard.attendance.title', $weeks, ['weeks' => $weeks]) }}
            </h2>
            <p class="text-sm text-slate-500">
                {{ __('admin.dashboard.attendance.total_records', ['count' => number_format($attendance['total'], 0, ',', '.')]) }}
            </p>
        </div>

        <div
            id="attendance-chart-data"
            class="hidden"
            data-attendance='@json($attendanceData)'
            data-label-present="{{ __('admin.dashboard.attendance.chart.present') }}"
            data-label-late="{{ __('admin.dashboard.attendance.chart.late') }}"
            data-label-absent="{{ __('admin.dashboard.attendance.chart.absent') }}"
        ></div>

        <div class="rounded-2xl border p-4" wire:ignore>
            <div class="mb-3 text-sm font-medium text-slate-700">
                {{ __('admin.dashboard.attendance.chart.title') }}
            </div>
            <div class="h-72">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border bg-emerald-50 p-4">
                <div class="text-sm text-emerald-600">{{ __('admin.dashboard.attendance.summary.present') }}</div>
                <div class="text-2xl font-bold">{{ number_format($attendance['present'], 0, ',', '.') }}</div>
                <div class="text-xs text-emerald-600">{{ $attendance['present_pct'] }}%</div>
            </div>

            <div class="rounded-xl border bg-yellow-50 p-4">
                <div class="text-sm text-yellow-600">{{ __('admin.dashboard.attendance.summary.late') }}</div>
                <div class="text-2xl font-bold">{{ number_format($attendance['late'], 0, ',', '.') }}</div>
                <div class="text-xs text-yellow-600">{{ $attendance['late_pct'] }}%</div>
            </div>

            <div class="rounded-xl border bg-red-50 p-4">
                <div class="text-sm text-red-600">{{ __('admin.dashboard.attendance.summary.absent') }}</div>
                <div class="text-2xl font-bold">{{ number_format($attendance['absent'], 0, ',', '.') }}</div>
                <div class="text-xs text-red-600">{{ $attendance['absent_pct'] }}%</div>
            </div>
        </div>
    </section>

    <section class="space-y-4 rounded-2xl border bg-white p-5">
        <h2 class="text-lg font-semibold">
            {{ __('admin.dashboard.problem_sessions.title') }}
        </h2>

        @forelse($problemSessions as $session)
            <div class="flex justify-between rounded-xl border p-4">
                <div>
                    <div class="font-medium">{{ $session->title }}</div>
                    <div class="text-xs text-slate-500">
                        {{ $session->topic?->name }} • {{ $session->topic?->course?->title }}
                    </div>
                </div>

                <div class="font-semibold text-red-600 text-sm">
                    {{ __('admin.dashboard.problem_sessions.absent_count', ['count' => number_format($session->absent_count, 0, ',', '.')]) }}
                </div>
            </div>
        @empty
            <div class="text-sm text-slate-500">
                {{ __('admin.dashboard.problem_sessions.empty') }}
            </div>
        @endforelse
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="space-y-4 rounded-2xl border bg-white p-5">
            <h2 class="text-lg font-semibold">{{ __('admin.dashboard.recent_sessions.title') }}</h2>

            @forelse($recentSessions as $session)
                <div class="flex items-start justify-between rounded-xl border p-4">
                    <div>
                        <div class="font-medium">{{ $session->title }}</div>

                        <div class="text-xs text-slate-500">
                            {{ $session->topic?->name ?? __('admin.dashboard.common.no_topic') }}
                            •
                            {{ $session->topic?->course?->title ?? __('admin.dashboard.common.no_course') }}
                        </div>

                        <div class="mt-1 text-xs text-slate-400">
                            {{ $session->start_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <span class="inline-flex items-center whitespace-nowrap self-start rounded bg-slate-100 px-2 py-1 text-xs">
                        {{ ucfirst($session->status) }}
                    </span>
                </div>
            @empty
                <div class="text-sm text-slate-500">
                    {{ __('admin.dashboard.recent_sessions.empty') }}
                </div>
            @endforelse
        </section>

        <section class="space-y-4 rounded-2xl border bg-white p-5">
            <h2 class="text-lg font-semibold">{{ __('admin.dashboard.upcoming_calendar.title') }}</h2>

            <div
                x-data="calendarComponent(@js($calendarSessions), @js($calendarWeekdays), @js($calendarMonths))"
                x-init="init()"
                class="space-y-4"
            >
                <div class="flex items-center justify-between">
                    <button @click="prevMonth()" class="rounded border px-3 py-1">{{ __('admin.dashboard.upcoming_calendar.prev') }}</button>
                    <div class="font-medium" x-text="monthYear"></div>
                    <button @click="nextMonth()" class="rounded border px-3 py-1">{{ __('admin.dashboard.upcoming_calendar.next') }}</button>
                </div>

                <div class="grid grid-cols-7 text-center text-xs text-slate-500">
                    <template x-for="day in weekdays" :key="day">
                        <div x-text="day"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-2 text-sm">
                    <template x-for="blank in blanks" :key="blank">
                        <div></div>
                    </template>

                    <template x-for="day in days" :key="day">
                        <div class="relative h-20 rounded-lg border p-1">
                            <div x-text="day" class="text-xs"></div>

                            <template x-if="hasSession(day)">
                                <div class="absolute bottom-1 left-1/2 -translate-x-1/2">
                                    <div class="h-2 w-2 rounded-full bg-blue-500"></div>
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
        function calendarComponent(sessions, weekdays, monthNames) {
            return {
                current: new Date(),
                sessions,
                weekdays,
                monthNames,
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

                    this.monthYear = `${this.monthNames[month] ?? ''} ${year}`.trim();
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
                    return {
                        present: 0,
                        late: 0,
                        absent: 0,
                        labels: {
                            present: 'Present',
                            late: 'Late',
                            absent: 'Absent',
                        },
                    };
                }

                try {
                    const parsed = JSON.parse(el.dataset.attendance || '{}');

                    return {
                        present: parsed.present ?? 0,
                        late: parsed.late ?? 0,
                        absent: parsed.absent ?? 0,
                        labels: {
                            present: el.dataset.labelPresent || 'Present',
                            late: el.dataset.labelLate || 'Late',
                            absent: el.dataset.labelAbsent || 'Absent',
                        },
                    };
                } catch (e) {
                    return {
                        present: 0,
                        late: 0,
                        absent: 0,
                        labels: {
                            present: 'Present',
                            late: 'Late',
                            absent: 'Absent',
                        },
                    };
                }
            }

            function renderAttendanceChart() {
                const canvas = document.getElementById('attendanceChart');

                if (!canvas || typeof Chart === 'undefined') {
                    return;
                }

                const data = getAttendanceData();

                if (attendanceChartInstance) {
                    attendanceChartInstance.data.labels = [
                        data.labels.present,
                        data.labels.late,
                        data.labels.absent,
                    ];
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
                        labels: [
                            data.labels.present,
                            data.labels.late,
                            data.labels.absent,
                        ],
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

                document.addEventListener('livewire:init', () => {
                    renderAttendanceChart();

                    if (window.Livewire && typeof window.Livewire.hook === 'function') {
                        window.Livewire.hook('morph.updated', () => {
                            renderAttendanceChart();
                        });
                    }
                });
            }
        })();
    </script>
@endpush