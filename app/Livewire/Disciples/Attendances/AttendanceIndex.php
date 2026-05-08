<?php

namespace App\Livewire\Disciples\Attendances;

use App\Livewire\Concerns\WithTableState;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use App\Models\VideoSession;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceIndex extends Component
{
    use WithTableState;

    public bool $showModal = false;
    public ?string $editingId = null;
    public ?string $selectedAttendanceId = null;
    public bool $isSaving = false;

    public string $video_session_id = '';
    public string $user_id = '';
    public string $status = 'present';
    public ?string $check_in_at = null;
    public ?string $clock_out_at = null;
    public ?string $ip_address = null;

    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $sessionFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'topicFilter' => ['except' => ''],
        'sessionFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'video_session_id' => 'required|exists:video_sessions,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:present,late,absent',
            'check_in_at' => 'nullable|date',
            'clock_out_at' => 'nullable|date|after_or_equal:check_in_at',
            'ip_address' => 'nullable|string|max:45',
        ];
    }

    public function mount(): void
    {
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function updatedTopicFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function updatedSessionFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAttendanceToFirstAvailable();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Attendance::with(['videoSession.topic.course', 'user'])->findOrFail($id);

        $this->editingId = $row->id;
        $this->video_session_id = $row->video_session_id;
        $this->user_id = $row->user_id;
        $this->status = $row->status;
        $this->check_in_at = $row->check_in_at?->format('Y-m-d\TH:i');
        $this->clock_out_at = $row->clock_out_at?->format('Y-m-d\TH:i');
        $this->ip_address = $row->ip_address;

        $this->selectedAttendanceId = $row->id;
        $this->showModal = true;
    }

    public function selectAttendance(string $id): void
    {
        $this->selectedAttendanceId = $id;
    }

    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            $this->validate();

            $session = VideoSession::with(['topic.course'])->findOrFail($this->video_session_id);

            if (! $session->topic || ! $session->topic->course) {
                $this->addError('video_session_id', 'Video session harus terhubung ke topic dan course.');
                return;
            }

            $checkIn = $this->check_in_at ? Carbon::parse($this->check_in_at) : null;
            $checkOut = $this->clock_out_at ? Carbon::parse($this->clock_out_at) : null;

            if (($this->status === 'present' || $this->status === 'late') && ! $checkIn) {
                $this->addError('check_in_at', 'Check in wajib diisi untuk status present atau late.');
                return;
            }

            if ($checkIn && ! $this->canClockIn($session, $checkIn)) {
                $this->addError('check_in_at', 'Clock-in berada di luar batas waktu sesi.');
                return;
            }

            if ($checkOut && ! $checkIn) {
                $this->addError('clock_out_at', 'Clock-out tidak bisa diisi tanpa check-in.');
                return;
            }

            if ($checkOut && $checkOut->gt($this->clockOutDeadline($session))) {
                $this->addError('clock_out_at', 'Clock-out melewati batas 15 menit sebelum sesi berakhir.');
                return;
            }

            if ($checkIn && $checkOut && $checkOut->lt($checkIn)) {
                $this->addError('clock_out_at', 'Clock-out harus sama atau lebih lambat dari check-in.');
                return;
            }

            $resolvedStatus = $this->resolveStatus($session, $checkIn, $this->status);

            Attendance::updateOrCreate(
                [
                    'video_session_id' => $this->video_session_id,
                    'user_id' => $this->user_id,
                ],
                [
                    'status' => $resolvedStatus,
                    'check_in_at' => $resolvedStatus === 'absent' ? null : $checkIn,
                    'clock_out_at' => $resolvedStatus === 'absent' ? null : $checkOut,
                    'ip_address' => $this->ip_address,
                ]
            );

            $this->selectedAttendanceId = Attendance::where('video_session_id', $this->video_session_id)
                ->where('user_id', $this->user_id)
                ->value('id');

            $this->resetForm();
            $this->showModal = false;
            $this->syncSelectedAttendanceToFirstAvailable();

            session()->flash('success', 'Attendance berhasil disimpan.');
        } finally {
            $this->isSaving = false;
        }
    }

    public function delete(string $id): void
    {
        Attendance::findOrFail($id)->delete();

        if ($this->selectedAttendanceId === $id) {
            $this->selectedAttendanceId = null;
            $this->syncSelectedAttendanceToFirstAvailable();
        }

        session()->flash('success', 'Attendance berhasil dihapus.');
    }

    public function setStatus(string $id, string $status): void
    {
        $attendance = Attendance::with('videoSession.topic.course')->findOrFail($id);
        $session = $attendance->videoSession;

        if (! $session || ! $session->topic || ! $session->topic->course) {
            session()->flash('error', 'Session tidak valid.');
            return;
        }

        if ($status === 'absent') {
            $attendance->update([
                'status' => 'absent',
                'check_in_at' => null,
                'clock_out_at' => null,
                'ip_address' => $attendance->ip_address ?? request()->ip(),
            ]);

            session()->flash('success', 'Status attendance diperbarui.');
            return;
        }

        $moment = $attendance->check_in_at ?? now();

        if (! $this->canClockIn($session, $moment)) {
            session()->flash('error', 'Waktu clock-in sudah melewati batas.');
            return;
        }

        $resolvedStatus = $this->resolveStatus($session, $moment, $status);

        $attendance->update([
            'status' => $resolvedStatus,
            'check_in_at' => $attendance->check_in_at ?? $moment,
            'ip_address' => $attendance->ip_address ?? request()->ip(),
        ]);

        session()->flash('success', 'Status attendance diperbarui.');
    }

    private function attendanceQuery()
    {
        return Attendance::with(['videoSession.topic.course', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('user', function ($u) {
                        $u->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })->orWhereHas('videoSession', function ($s) {
                        $s->where('title', 'like', '%' . $this->search . '%')
                            ->orWhereHas('topic', fn ($t) => $t->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('topic.course', fn ($c) => $c->where('title', 'like', '%' . $this->search . '%'));
                    });
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->whereHas('videoSession.topic', fn ($t) => $t->where('course_id', $this->courseFilter)))
            ->when($this->topicFilter, fn ($q) => $q->whereHas('videoSession', fn ($s) => $s->where('topic_id', $this->topicFilter)))
            ->when($this->sessionFilter, fn ($q) => $q->where('video_session_id', $this->sessionFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));
    }

    private function syncSelectedAttendanceToFirstAvailable(): void
    {
        $this->selectedAttendanceId = (clone $this->attendanceQuery())
            ->latest()
            ->value('id');
    }

    private function resolveStatus(VideoSession $session, ?Carbon $checkIn, string $requestedStatus = 'present'): string
    {
        if ($requestedStatus === 'absent') {
            return 'absent';
        }

        if (! $checkIn) {
            return 'absent';
        }

        if (! $this->canClockIn($session, $checkIn)) {
            return 'absent';
        }

        $minutesAfterStart = $session->start_at->diffInMinutes($checkIn, false);

        return $minutesAfterStart <= 15 ? 'present' : 'late';
    }

    private function canClockIn(VideoSession $session, Carbon $moment): bool
    {
        return $moment->betweenIncluded($session->start_at, $this->clockInDeadline($session));
    }

    private function clockInDeadline(VideoSession $session): Carbon
    {
        $byStartWindow = $session->start_at->copy()->addMinutes(45);
        $byEndWindow = $session->end_at->copy()->subMinutes(15);

        return $byStartWindow->lt($byEndWindow) ? $byStartWindow : $byEndWindow;
    }

    private function clockOutDeadline(VideoSession $session): Carbon
    {
        return $session->end_at->copy()->subMinutes(15);
    }

    public function timingLabel(Attendance $attendance): string
    {
        if (! $attendance->check_in_at) {
            return 'No clock-in';
        }

        $session = $attendance->videoSession;

        if (! $session) {
            return ucfirst($attendance->status);
        }

        if (! $this->canClockIn($session, $attendance->check_in_at)) {
            return 'Outside window';
        }

        $minutesAfterStart = $session->start_at->diffInMinutes($attendance->check_in_at, false);

        return $minutesAfterStart <= 15 ? 'On time' : 'Late';
    }

    public function timingTone(Attendance $attendance): string
    {
        return match ($this->timingLabel($attendance)) {
            'On time' => 'emerald',
            'Late' => 'amber',
            'Outside window' => 'rose',
            default => 'slate',
        };
    }

    public function timingBadgeClass(Attendance $attendance): string
    {
        return match ($this->timingLabel($attendance)) {
            'On time' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Late' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Outside window' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    public function statusBadgeClass(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'present' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'late' => 'bg-amber-50 text-amber-700 border-amber-200',
            'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    public function platformLabel(Attendance $attendance): string
    {
        $session = $attendance->videoSession;

        if (! $session) {
            return 'Unknown';
        }

        return $session->zoom_link ? 'Zoom' : 'Offline';
    }

    public function windowLabel(Attendance $attendance): string
    {
        $session = $attendance->videoSession;

        if (! $session) {
            return '-';
        }

        return 'Clock-in ≤ ' . $this->clockInDeadline($session)->format('H:i')
            . ' · Clock-out ≤ ' . $this->clockOutDeadline($session)->format('H:i');
    }

    public function render()
    {
        $baseQuery = $this->attendanceQuery();

        $rows = (clone $baseQuery)->latest()->paginate($this->perPage);

        $rows->getCollection()->transform(function (Attendance $row) {
            $row->setAttribute('timing_label', $this->timingLabel($row));
            $row->setAttribute('timing_badge_class', $this->timingBadgeClass($row));
            $row->setAttribute('status_badge_class', $this->statusBadgeClass($row->status));
            $row->setAttribute('platform_label', $this->platformLabel($row));
            $row->setAttribute('window_label', $this->windowLabel($row));

            return $row;
        });

        $selectedAttendance = $this->selectedAttendanceId
            ? Attendance::with(['videoSession.topic.course', 'user'])->find($this->selectedAttendanceId)
            : null;

        if ($selectedAttendance) {
            $selectedAttendance->setAttribute('timing_label', $this->timingLabel($selectedAttendance));
            $selectedAttendance->setAttribute('timing_badge_class', $this->timingBadgeClass($selectedAttendance));
            $selectedAttendance->setAttribute('status_badge_class', $this->statusBadgeClass($selectedAttendance->status));
            $selectedAttendance->setAttribute('platform_label', $this->platformLabel($selectedAttendance));
            $selectedAttendance->setAttribute('window_label', $this->windowLabel($selectedAttendance));
        }

        $total = (clone $baseQuery)->count();
        $present = (clone $baseQuery)->where('status', 'present')->count();
        $late = (clone $baseQuery)->where('status', 'late')->count();
        $absent = (clone $baseQuery)->where('status', 'absent')->count();

        $statsCards = [
            [
                'label' => 'Total Records',
                'value' => number_format($total),
                'note' => $total
                    ? 'Across filtered attendance records.'
                    : 'No attendance records yet.',
            ],
            [
                'label' => 'Present',
                'value' => number_format($present),
                'note' => $total ? round(($present / $total) * 100, 1) . '% of records.' : '—',
            ],
            [
                'label' => 'Late',
                'value' => number_format($late),
                'note' => $total ? round(($late / $total) * 100, 1) . '% of records.' : '—',
            ],
            [
                'label' => 'Absent',
                'value' => number_format($absent),
                'note' => $total ? round(($absent / $total) * 100, 1) . '% of records.' : '—',
            ],
        ];

        return view('livewire.disciples.attendances.index', [
            'rows' => $rows,
            'selectedAttendance' => $selectedAttendance,
            'courses' => Course::orderBy('title')->get(),
            'topics' => Topic::with('course')->orderBy('name')->get(),
            'sessions' => VideoSession::with('topic.course')->orderByDesc('start_at')->get(),
            'users' => User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
                ->orderBy('name')
                ->get(),
            'statsCards' => $statsCards,
        ])->layout('layouts.learning');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'video_session_id',
            'user_id',
            'status',
            'check_in_at',
            'clock_out_at',
            'ip_address',
        ]);

        $this->status = 'present';
        $this->ip_address = request()->ip();
    }
}