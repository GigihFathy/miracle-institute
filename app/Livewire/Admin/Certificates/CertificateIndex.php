<?php

namespace App\Livewire\Admin\Certificates;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class CertificateIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;

    public ?string $editingId = null;
    public string $certificate_number = '';
    public string $user_id = '';
    public string $course_id = '';
    public ?string $issued_at = null;
    public string $status = 'issued';

    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $typeFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'topicFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'certificate_number' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'issued_at' => 'nullable|date',
            'status' => 'required|string|max:50',
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

    public function updatedTypeFilter(): void
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
        $row = Certificate::findOrFail($id);

        $this->editingId = $row->id;
        $this->certificate_number = $row->certificate_number;
        $this->user_id = $row->user_id;
        $this->course_id = $row->course_id ?? '';
        $this->issued_at = optional($row->issued_at)->format('Y-m-d\TH:i');
        $this->status = $row->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $number = $this->certificate_number ?: ('CERT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)));

        Certificate::updateOrCreate(
            ['id' => $this->editingId],
            [
                'certificate_number' => $number,
                'user_id' => $this->user_id,
                'course_id' => $this->course_id ?: null,
                'issued_at' => $this->issued_at ? Carbon::parse($this->issued_at) : now(),
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Certificate berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Certificate::findOrFail($id)->delete();
        session()->flash('success', 'Certificate berhasil dihapus.');
    }

    public function render()
    {
        $baseQuery = Certificate::with(['user', 'course'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('certificate_number', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        return view('livewire.admin.certificates.index', [
            'rows' => (clone $baseQuery)->latest()->paginate($this->perPage),
            'courses' => Course::orderBy('title')->get(),
            'topics' => Topic::with('course')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'selectedTopic' => $this->topicFilter ? Topic::with('course')->find($this->topicFilter) : null,
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'issued' => (clone $baseQuery)->where('status', 'issued')->count(),
                'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
                'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
            ],
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'certificate_number',
            'user_id',
            'course_id',
            'issued_at',
            'status',
        ]);

        $this->status = 'issued';
    }
}