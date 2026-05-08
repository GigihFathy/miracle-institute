<?php

namespace App\Livewire\Disciples\Sessions;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\Topic;
use App\Models\VideoSession;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SessionIndex extends Component
{
    use WithTableState;

    public bool $showModal = false;
    public ?string $editingId = null;
    public ?string $selectedSessionId = null;
    public bool $isSaving = false;

    public string $topic_id = '';
    public string $title = '';
    public string $zoom_link = '';
    public ?string $record_link = null;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?string $status = 'scheduled';

    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'topicFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 12],
    ];

    protected function rules(): array
    {
        return [
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'zoom_link' => 'required|url|max:255',
            'record_link' => 'nullable|url|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ];
    }

    public function mount(): void
    {
        $this->syncSelectedSessionToFirstAvailable();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->syncSelectedSessionToFirstAvailable();
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedSessionToFirstAvailable();
    }

    public function updatedTopicFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedSessionToFirstAvailable();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedSessionToFirstAvailable();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = VideoSession::findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->title = $row->title;
        $this->zoom_link = $row->zoom_link;
        $this->record_link = $row->record_link;
        $this->start_at = optional($row->start_at)->format('Y-m-d\TH:i');
        $this->end_at = optional($row->end_at)->format('Y-m-d\TH:i');
        $this->status = $row->status;

        $this->selectedSessionId = $row->id;
        $this->showModal = true;
    }

    public function selectSession(string $id): void
    {
        $this->selectedSessionId = $id;
    }

    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            $this->validate();
            $this->validateScheduleConflict();

            VideoSession::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'topic_id' => $this->topic_id,
                    'title' => $this->title,
                    'zoom_link' => $this->zoom_link,
                    'record_link' => $this->record_link ?: null,
                    'start_at' => Carbon::parse($this->start_at),
                    'end_at' => Carbon::parse($this->end_at),
                    'status' => $this->status,
                ]
            );

            $this->resetForm();
            $this->showModal = false;
            $this->syncSelectedSessionToFirstAvailable();

            session()->flash('success', 'Session berhasil disimpan.');
        } finally {
            $this->isSaving = false;
        }
    }

    public function delete(string $id): void
    {
        VideoSession::findOrFail($id)->delete();

        if ($this->selectedSessionId === $id) {
            $this->syncSelectedSessionToFirstAvailable();
        }

        session()->flash('success', 'Session berhasil dihapus.');
    }

    public function render()
    {
        $baseQuery = VideoSession::with(['topic.course', 'attendances'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('zoom_link', 'like', '%' . $this->search . '%')
                        ->orWhere('record_link', 'like', '%' . $this->search . '%')
                        ->orWhereHas('topic', fn ($t) => $t->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('topic.course', fn ($c) => $c->where('title', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->whereHas('topic', fn ($t) => $t->where('course_id', $this->courseFilter)))
            ->when($this->topicFilter, fn ($q) => $q->where('topic_id', $this->topicFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $rows = (clone $baseQuery)->latest('start_at')->paginate($this->perPage);

        $rows->getCollection()->transform(function (VideoSession $session) {
            $session->setAttribute('platform_label', $this->platformLabel($session));
            $session->setAttribute('platform_badge_class', $this->platformBadgeClass($session));
            $session->setAttribute('schedule_text', $this->scheduleText($session));
            $session->setAttribute('duration_text', $this->durationText($session));
            $session->setAttribute('relevance_label', 'Linked to topic');
            $session->setAttribute('relevance_badge_class', 'bg-emerald-50 text-emerald-700 border-emerald-200');
            $session->setAttribute('attendance_total', $session->attendances->count());
            $session->setAttribute('attendance_present', $session->attendances->where('status', 'present')->count());
            $session->setAttribute('attendance_late', $session->attendances->where('status', 'late')->count());
            $session->setAttribute('attendance_absent', $session->attendances->where('status', 'absent')->count());

            return $session;
        });

        $selectedSession = $this->selectedSessionId
            ? VideoSession::with(['topic.course', 'attendances.user'])->find($this->selectedSessionId)
            : null;

        if ($selectedSession) {
            $selectedSession->setAttribute('platform_label', $this->platformLabel($selectedSession));
            $selectedSession->setAttribute('platform_badge_class', $this->platformBadgeClass($selectedSession));
            $selectedSession->setAttribute('schedule_text', $this->scheduleText($selectedSession));
            $selectedSession->setAttribute('duration_text', $this->durationText($selectedSession));
            $selectedSession->setAttribute('relevance_label', 'Linked to topic');
            $selectedSession->setAttribute('relevance_badge_class', 'bg-emerald-50 text-emerald-700 border-emerald-200');

            $totalAttendance = $selectedSession->attendances->count();
            $present = $selectedSession->attendances->where('status', 'present')->count();
            $late = $selectedSession->attendances->where('status', 'late')->count();
            $absent = $selectedSession->attendances->where('status', 'absent')->count();

            $selectedSession->setAttribute('attendance_total', $totalAttendance);
            $selectedSession->setAttribute('attendance_present', $present);
            $selectedSession->setAttribute('attendance_late', $late);
            $selectedSession->setAttribute('attendance_absent', $absent);
            $selectedSession->setAttribute(
                'attendance_rate',
                $totalAttendance > 0 ? (int) round((($present + $late) / $totalAttendance) * 100) : 0
            );
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'scheduled' => (clone $baseQuery)->where('status', 'scheduled')->count(),
            'ongoing' => (clone $baseQuery)->where('status', 'ongoing')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        $statsCards = [
            [
                'label' => 'Total Sessions',
                'value' => number_format($stats['total']),
                'note' => 'Seluruh sesi dalam scope filter aktif.',
            ],
            [
                'label' => 'Scheduled',
                'value' => number_format($stats['scheduled']),
                'note' => 'Sesi yang menunggu jadwal mulai.',
            ],
            [
                'label' => 'Ongoing',
                'value' => number_format($stats['ongoing']),
                'note' => 'Sesi yang sedang berlangsung.',
            ],
            [
                'label' => 'Completed',
                'value' => number_format($stats['completed']),
                'note' => 'Sesi yang telah selesai.',
            ],
            [
                'label' => 'Cancelled',
                'value' => number_format($stats['cancelled']),
                'note' => 'Sesi yang dibatalkan.',
            ],
            [
                'label' => 'Platform Mix',
                'value' => $this->platformMixText($baseQuery),
                'note' => 'Komposisi platform yang dipakai (Zoom/Google/Online).',
            ],
        ];

        return view('livewire.disciples.sessions.index', [
            'rows' => $rows,
            'selectedSession' => $selectedSession,
            'courses' => Course::orderBy('title')->get(),
            'topics' => Topic::with('course')->orderBy('name')->get(),
            'statsCards' => $statsCards,
        ])->layout('layouts.learning');
    }

    private function validateScheduleConflict(): void
    {
        if (! $this->topic_id || ! $this->start_at || ! $this->end_at) {
            return;
        }

        $start = Carbon::parse($this->start_at);
        $end = Carbon::parse($this->end_at);

        $conflict = VideoSession::query()
            ->where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->where('start_at', '<=', $start)
                            ->where('end_at', '>=', $end);
                    });
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_at' => 'Jadwal sesi bertabrakan dengan sesi lain pada topic yang sama.',
            ]);
        }
    }

    private function platformLabel(VideoSession $session): string
    {
        $link = strtolower((string) $session->zoom_link);

        if (str_contains($link, 'zoom.us')) {
            return 'Zoom';
        }

        if (str_contains($link, 'meet.google') || str_contains($link, 'google.com')) {
            return 'Google Meet';
        }

        return 'Online';
    }

    private function platformBadgeClass(VideoSession $session): string
    {
        return match ($this->platformLabel($session)) {
            'Zoom' => 'bg-blue-50 text-blue-700 border-blue-200',
            'Google Meet' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    private function platformMixText($baseQuery): string
    {
        $items = (clone $baseQuery)->get();

        $zoom = $items->filter(fn ($session) => $this->platformLabel($session) === 'Zoom')->count();
        $meet = $items->filter(fn ($session) => $this->platformLabel($session) === 'Google Meet')->count();
        $online = $items->filter(fn ($session) => $this->platformLabel($session) === 'Online')->count();

        return $zoom . '/' . $meet . '/' . $online;
    }

    private function scheduleText(VideoSession $session): string
    {
        if (! $session->start_at || ! $session->end_at) {
            return 'N/A';
        }

        return $session->start_at->format('d M Y, H:i') . ' - ' . $session->end_at->format('H:i');
    }

    private function durationText(VideoSession $session): string
    {
        if (! $session->start_at || ! $session->end_at) {
            return 'N/A';
        }

        $minutes = max(0, $session->start_at->diffInMinutes($session->end_at));

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours > 0 && $remaining > 0) {
            return $hours . 'h ' . $remaining . 'm';
        }

        if ($hours > 0) {
            return $hours . 'h';
        }

        return $remaining . 'm';
    }

    private function syncSelectedSessionToFirstAvailable(): void
    {
        $this->selectedSessionId = VideoSession::query()
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('zoom_link', 'like', '%' . $this->search . '%')
                        ->orWhere('record_link', 'like', '%' . $this->search . '%')
                        ->orWhereHas('topic', fn ($t) => $t->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('topic.course', fn ($c) => $c->where('title', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->whereHas('topic', fn ($t) => $t->where('course_id', $this->courseFilter)))
            ->when($this->topicFilter, fn ($q) => $q->where('topic_id', $this->topicFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('start_at')
            ->value('id');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'topic_id',
            'title',
            'zoom_link',
            'record_link',
            'start_at',
            'end_at',
            'status',
        ]);

        $this->status = 'scheduled';
    }
}