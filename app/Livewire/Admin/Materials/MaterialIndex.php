<?php

namespace App\Livewire\Admin\Materials;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MaterialIndex extends Component
{
    use WithAdminTableState;

    public array $openTopics = [];

    public ?string $editingId = null;
    public string $topic_id = '';
    public string $uploader_id = '';
    public string $name = '';
    public string $visibility = 'Public';
    public string $path = '';
    public string $external_url = '';
    public string $type = 'pdf';
    public string $status = 'active';
    public int $sort_order = 0;

    public string $search = '';
    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $typeFilter = '';
    public string $visibilityFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'topicFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'visibilityFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'topic_id' => 'required|exists:topics,id',
            'uploader_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:255',
            'type' => 'required|string|max:50',
            'visibility' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
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

    public function toggleTopic(string $id): void
    {
        if (in_array($id, $this->openTopics, true)) {
            $this->openTopics = array_values(array_diff($this->openTopics, [$id]));
            return;
        }

        $this->openTopics[] = $id;
    }

    public function create(?string $topicId = null): void
    {
        $this->resetForm();

        if ($topicId) {
            $this->topic_id = $topicId;
            if (!in_array($topicId, $this->openTopics, true)) {
                $this->openTopics[] = $topicId;
            }
        }

        if (auth()->check()) {
            $this->uploader_id = auth()->id();
        }
    }

    public function edit(string $id): void
    {
        $row = Material::findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->uploader_id = $row->uploader_id;
        $this->name = $row->name;
        $this->path = $row->path ?? '';
        $this->external_url = $row->external_url ?? '';
        $this->type = $row->type;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);

        if (!in_array($row->topic_id, $this->openTopics, true)) {
            $this->openTopics[] = $row->topic_id;
        }
    }

    public function save(): void
    {
        $this->validate();
        $this->validateMaterialType();

        $count = Material::where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->count();

        if (!$this->editingId && $count >= 3) {
            throw ValidationException::withMessages([
                'topic_id' => 'Setiap topic hanya boleh memiliki 3 material.',
            ]);
        }

        Material::updateOrCreate(
            ['id' => $this->editingId],
            [
                'topic_id' => $this->topic_id,
                'uploader_id' => $this->uploader_id,
                'name' => $this->name,
                'path' => $this->path,
                'external_url' => $this->external_url,
                'type' => $this->type,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ]
        );

        $this->resetForm();

        if ($this->topic_id && !in_array($this->topic_id, $this->openTopics, true)) {
            $this->openTopics[] = $this->topic_id;
        }

        session()->flash('success', 'Material berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Material::findOrFail($id)->delete();
        session()->flash('success', 'Material berhasil dihapus.');
    }

    public function render()
    {
        $topics = Topic::with([
            'course',
            'materials' => function ($q) {
                $q->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
                    ->when($this->visibilityFilter, fn ($q) => $q->where('visibility', $this->visibilityFilter))
                    ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('sort_order')
                    ->orderBy('name');
            },
        ])
        ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
        ->when($this->topicFilter, fn ($q) => $q->where('id', $this->topicFilter))
        ->when($this->search, function ($q) {
            $q->where(function ($inner) {
                $inner->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', "%{$this->search}%"))
                    ->orWhereHas('materials', fn ($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        })
        ->orderBy('course_id')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view('livewire.admin.materials.index', [
            'topics' => $topics,
            'courses' => Course::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.admin');
    }

    private function validateMaterialType(): void
    {
        if (!$this->topic_id || !$this->type) {
            return;
        }

        $exists = Material::where('topic_id', $this->topic_id)
            ->where('type', $this->type)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'type' => 'Tipe material ini sudah ada di topic ini.',
            ]);
        }
    }

    public function getAvailableTypesProperty(): array
    {
        $all = ['pdf', 'ppt', 'video'];

        $used = Material::where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->pluck('type')
            ->toArray();

        return array_values(array_diff($all, $used));
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'topic_id',
            'uploader_id',
            'name',
            'path',
            'external_url',
            'type',
            'visibility',
            'status',
            'sort_order',
        ]);

        $this->visibility = 'Public';
        $this->type = 'pdf';
        $this->status = 'active';
        $this->sort_order = 0;
    }
}