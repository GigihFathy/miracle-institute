<?php

namespace App\Livewire\Admin\Sessions;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\Topic;
use App\Models\VideoSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class VideoSessionIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public ?string $editingId = null;

    public ?string $topic_id = null;
    public string $topicSearch = '';
    public bool $showTopicResults = true;

    public string $title = '';
    public string $zoom_link = '';
    public ?string $start_at = null;
    public ?string $end_at = null;
    public string $status = 'scheduled';

    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount(?string $topicFilter = null): void
    {
        $this->topicFilter = $topicFilter ?? '';
    }

    protected function rules(): array
    {
        return [
            'topic_id' => [
                'required',
                'exists:topics,id',
                Rule::unique('video_sessions', 'topic_id')->ignore($this->editingId),
            ],
            'title' => 'required|string|max:255',
            'zoom_link' => 'required|url|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
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

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTopicSearch(): void
    {
        $this->showTopicResults = true;

        if (trim($this->topicSearch) === '') {
            $this->topic_id = null;
        } else {
            $this->topic_id = null;
        }
    }

    public function updatedStartAt(): void
    {
        $this->syncStatusPreview();
    }

    public function updatedEndAt(): void
    {
        $this->syncStatusPreview();
    }

    public function selectTopic(string $id): void
    {
        $topic = Topic::with('course')->find($id);

        if (!$topic) {
            return;
        }

        $this->topic_id = $topic->id;
        $this->topicSearch = trim(
            ($topic->course?->title ? $topic->course->title . ' · ' : '') . $topic->name
        );
        $this->showTopicResults = false;
    }

    public function clearTopicSelection(): void
    {
        $this->topic_id = null;
        $this->topicSearch = '';
        $this->showTopicResults = true;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->topic_id = $this->topicFilter ?: null;
        if ($this->topic_id) {
            $topic = Topic::with('course')->find($this->topic_id);
            if ($topic) {
                $this->topicSearch = trim(($topic->course?->title ? $topic->course->title . ' · ' : '') . $topic->name);
                $this->showTopicResults = false;
            }
        }
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = VideoSession::with('topic.course')->findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->topicSearch = trim(
            ($row->topic?->course?->title ? $row->topic->course->title . ' · ' : '') . ($row->topic?->name ?? '')
        );
        $this->showTopicResults = false;

        $this->title = $row->title;
        $this->zoom_link = $row->zoom_link;
        $this->start_at = optional($row->start_at)->format('Y-m-d\TH:i');
        $this->end_at = optional($row->end_at)->format('Y-m-d\TH:i');
        $this->status = $row->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $startAt = Carbon::parse($this->start_at);
        $endAt = Carbon::parse($this->end_at);

        $status = $this->resolveStatus($startAt, $endAt, $this->status);

        VideoSession::updateOrCreate(
            ['id' => $this->editingId],
            [
                'topic_id' => $this->topic_id,
                'title' => $this->title,
                'zoom_link' => $this->zoom_link,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => $status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Session berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        VideoSession::findOrFail($id)->delete();
        session()->flash('success', 'Session berhasil dihapus.');
    }

    public function render()
    {
        $baseQuery = VideoSession::with(['topic.course', 'attendances'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', "%{$this->search}%")
                        ->orWhere('zoom_link', 'like', "%{$this->search}%")
                        ->orWhereHas('topic', fn ($t) => $t->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('topic.course', fn ($c) => $c->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->whereHas('topic', fn ($t) => $t->where('course_id', $this->courseFilter)))
            ->when($this->topicFilter, fn ($q) => $q->where('topic_id', $this->topicFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $selectedTopic = $this->topicFilter
            ? Topic::with('course')->find($this->topicFilter)
            : null;

        $selectedSession = $this->topicFilter
            ? VideoSession::with(['topic.course', 'attendances.user'])->where('topic_id', $this->topicFilter)->first()
            : null;

        return view('livewire.admin.sessions.index', [
            'rows' => (clone $baseQuery)->latest('start_at')->paginate($this->perPage),
            'courses' => Course::orderBy('title')->get(),
            'topics' => Topic::with('course')->orderBy('name')->get(),
            'topicOptions' => $this->topicOptions,
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'selectedFilterTopic' => $selectedTopic,
            'selectedFilterSession' => $selectedSession,
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'scheduled' => (clone $baseQuery)->where('status', 'scheduled')->count(),
                'ongoing' => (clone $baseQuery)->where('status', 'ongoing')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
                'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            ],
        ])->layout('layouts.admin');
    }

    public function getTopicOptionsProperty(): Collection
    {
        return Topic::with('course')
            ->when($this->topicSearch, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->topicSearch}%")
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$this->topicSearch}%"));
                });
            })
            ->orderBy('name')
            ->limit(30)
            ->get();
    }

    public function getSelectedTopicProperty(): ?Topic
    {
        if (!$this->topic_id) {
            return null;
        }

        return Topic::with('course')->find($this->topic_id);
    }

    private function resolveStatus(
        Carbon $startAt,
        Carbon $endAt,
        ?string $currentStatus = null
    ): string {
        if ($currentStatus === 'cancelled') {
            return 'cancelled';
        }

        $now = now()->seconds(0);
        $startAt = $startAt->copy()->seconds(0);
        $endAt = $endAt->copy()->seconds(0);

        if ($now->lt($startAt)) {
            return 'scheduled';
        }


        if ($now->gte($startAt) && $now->lte($endAt)) {
            return 'ongoing';
        }

        return 'completed';
    }

    private function syncStatusPreview(): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        if (!$this->start_at || !$this->end_at) {
            return;
        }

        try {
            $startAt = Carbon::parse($this->start_at);
            $endAt = Carbon::parse($this->end_at);

            $this->status = $this->resolveStatus(
                $startAt,
                $endAt,
                $this->status
            );
        } catch (\Throwable $e) {
            //
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'topic_id',
            'topicSearch',
            'title',
            'zoom_link',
            'start_at',
            'end_at',
            'status',
        ]);

        $this->status = 'scheduled';
        $this->showTopicResults = true;
    }
}