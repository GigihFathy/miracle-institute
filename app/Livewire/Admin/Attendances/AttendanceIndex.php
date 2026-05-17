<?php

namespace App\Livewire\Admin\Attendances;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use App\Models\VideoSession;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public ?string $editingId = null;

    public string $video_session_id = '';
    public string $user_id = '';
    public string $status = 'present';
    public ?string $check_in_at = null;
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
            'ip_address' => 'nullable|string|max:45',
        ];
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTopicFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSessionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Attendance::findOrFail($id);

        $this->editingId = $row->id;
        $this->video_session_id = $row->video_session_id;
        $this->user_id = $row->user_id;
        $this->status = $row->status;
        $this->check_in_at = optional($row->check_in_at)->format('Y-m-d\TH:i');
        $this->ip_address = $row->ip_address;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Attendance::updateOrCreate(
            [
                'video_session_id' => $this->video_session_id,
                'user_id' => $this->user_id,
            ],
            [
                'status' => $this->status,
                'check_in_at' => $this->check_in_at ? Carbon::parse($this->check_in_at) : null,
                'ip_address' => $this->ip_address,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        
        session()->flash('success', 'Attendance berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Attendance::findOrFail($id)->delete();
        session()->flash('success', 'Attendance berhasil dihapus.');
    }

    public function setStatus(string $id, string $status): void
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'status' => $status,
            'check_in_at' => $attendance->check_in_at ?? now(),
        ]);

        session()->flash('success', 'Status attendance diperbarui.');
    }

    public function render()
    {
        $baseQuery = Attendance::with(['videoSession.topic.course', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('user', function ($u) {
                        $u->where('name', 'like', "%{$this->search}%")
                            ->orWhere('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    })->orWhereHas('videoSession', function ($s) {
                        $s->where('title', 'like', "%{$this->search}%")
                            ->orWhereHas('topic', fn ($t) => $t->where('name', 'like', "%{$this->search}%"))
                            ->orWhereHas('topic.course', fn ($c) => $c->where('title', 'like', "%{$this->search}%"));
                    });
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->whereHas('videoSession.topic', fn ($t) => $t->where('course_id', $this->courseFilter)))
            ->when($this->topicFilter, fn ($q) => $q->whereHas('videoSession', fn ($s) => $s->where('topic_id', $this->topicFilter)))
            ->when($this->sessionFilter, fn ($q) => $q->where('video_session_id', $this->sessionFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        return view('livewire.admin.attendances.index', [
            'rows' => (clone $baseQuery)->latest()->paginate($this->perPage),
            'courses' => Course::orderBy('title')->get(),
            'topics' => Topic::with('course')->orderBy('name')->get(),
            'sessions' => VideoSession::with('topic.course')->orderByDesc('start_at')->get(),
            'users' => User::orderBy('name')->get(),
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'selectedTopic' => $this->topicFilter ? Topic::with('course')->find($this->topicFilter) : null,
            'selectedSession' => $this->sessionFilter ? VideoSession::with('topic.course')->find($this->sessionFilter) : null,
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'present' => (clone $baseQuery)->where('status', 'present')->count(),
                'late' => (clone $baseQuery)->where('status', 'late')->count(),
                'absent' => (clone $baseQuery)->where('status', 'absent')->count(),
            ],
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'video_session_id',
            'user_id',
            'status',
            'check_in_at',
            'ip_address',
        ]);

        $this->status = 'present';

        if (auth()->check()) {
            $this->ip_address = request()->ip();
        }
    }
}