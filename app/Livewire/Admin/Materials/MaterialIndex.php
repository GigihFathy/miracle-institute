<?php

namespace App\Livewire\Admin\Materials;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use App\Services\Materials\MaterialAssetService;
use App\Services\Materials\YoutubeService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;

class MaterialIndex extends Component
{
    use WithAdminTableState;
    use WithFileUploads;

    public bool $showModal = false;
    public bool $uploading = false;
    public array $openTopics = [];

    public ?string $editingId = null;
    public ?string $topic_id = null;
    public string $name = '';
    public string $visibility = 'public';
    public string $status = 'active';
    public int $sort_order = 0;
    public mixed $materialFile = null;
    public string $external_url = '';
    public ?string $type = null;

    public string $search = '';
    public string $courseFilter = '';
    public string $topicFilter = '';
    public string $typeFilter = '';
    public string $visibilityFilter = '';
    public string $statusFilter = '';

    protected $listeners = ['materialsUpdated' => '$refresh'];

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
            'topic_id' => ['required', 'exists:topics,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Material::TYPES)],
            'visibility' => ['required', Rule::in(Material::VISIBILITIES)],
            'status' => ['required', Rule::in(Material::STATUSES)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'materialFile' => ['nullable', 'file', 'max:51200'],
            'external_url' => [
                'nullable',
                Rule::requiredIf($this->type === 'video'),
                $this->type === 'video' ? 'url' : '',
                'max:2048'
            ],
        ];
    }

    public function updatedMaterialFile(): void
    {
        $this->uploading = true;
    }

    public function updatedType(): void
    {
        $this->materialFile = null;
        $this->external_url = '';
    }

    public function updatedTopicId(): void
    {
        $this->updatedType();
    }

    public function toggleTopic(string $id): void
    {
        if (in_array($id, $this->openTopics, true)) {
            $this->openTopics = array_values(array_diff($this->openTopics, [$id]));
        } else {
            $this->openTopics[] = $id;
        }
    }

    public function create(?string $topicId = null): void
    {
        $this->resetForm();

        if ($topicId) {
            $this->topic_id = $topicId;
            if (!in_array($topicId, $this->openTopics, true)) {
                $this->openTopics[] = $topicId;
            }
            $this->type = $this->availableTypes[0] ?? null;
        }

        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Material::findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->name = $row->name;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);
        $this->type = $row->type;
        $this->external_url = $row->external_url ?? '';
        $this->materialFile = null;

        if (!in_array($row->topic_id, $this->openTopics, true)) {
            $this->openTopics[] = $row->topic_id;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $material = $this->editingId
            ? Material::findOrFail($this->editingId)
            : null;

        // max 3 materials per topic
        if (!$this->editingId) {
            $currentCount = Material::query()
                ->where('topic_id', $this->topic_id)
                ->count();
            if ($currentCount >= 3) {
                throw ValidationException::withMessages([
                    'topic_id' => 'Setiap topic hanya boleh memiliki maksimal 3 material.',
                ]);
            }
        }

        // duplicate type prevention
        $duplicateCheck = Material::query()
            ->where('topic_id', $this->topic_id)
            ->where('type', $this->type)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($duplicateCheck) {
            throw ValidationException::withMessages([
                'type' => 'Tipe material "' . strtoupper((string) $this->type) . '" sudah ada di topic ini.',
            ]);
        }

        try {
            $asset = app(MaterialAssetService::class)->sync(
                material: $material,
                type: (string) $this->type,
                file: $this->materialFile,
                externalUrl: $this->external_url ?: null,
                title: $this->name
            );
        } catch (RuntimeException $e) {
            $this->addError('materialFile', 'Upload gagal: ' . $e->getMessage());
            $this->uploading = false;
            return;
        }

        $payload = [
            'topic_id' => $this->topic_id,
            'uploader_id' => auth()->id(),
            'name' => $this->name,
            'type' => $this->type,
            'path' => $asset['path'],
            'external_url' => $asset['external_url'],
            'visibility' => $this->visibility,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ];

        if ($material) {
            $material->update($payload);
        } else {
            Material::create($payload);
        }

        $this->uploading = false;
        $this->resetForm();
        $this->dispatch('materialsUpdated');

        session()->flash('success', 'Material berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        $material = Material::findOrFail($id);

        app(MaterialAssetService::class)->delete($material);
        $material->delete();

        $this->resetForm();
        $this->dispatch('materialsUpdated');

        session()->flash('success', 'Material berhasil dihapus.');
    }

    public function render()
    {
        $topics = Topic::query()
            ->with([
                'course',
                'materials' => fn ($q) => $q
                    ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
                    ->when($this->visibilityFilter, fn ($q) => $q->where('visibility', $this->visibilityFilter))
                    ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('sort_order')
                    ->orderBy('created_at'),
            ])
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->topicFilter, fn ($q) => $q->where('id', $this->topicFilter))
            ->when($this->search, fn ($q) => $q->where(function ($inner) {
                $inner->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', "%{$this->search}%"))
                    ->orWhereHas('materials', fn ($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            }))
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

      
        $topics->each(function ($topic) {
            $topic->materials->each(function ($material) {
                if ($material->type === 'video' && $material->external_url) {
                    $videoId = app(MaterialAssetService::class)
                        ->youtube
                        ->extractVideoId($material->external_url);
                    $material->thumbnail_url = $videoId
                        ? "https://img.youtube.com/vi/$videoId/hqdefault.jpg"
                        : null;
                }
            });
        });

        return view('livewire.admin.materials.index', [
            'topics' => $topics,
            'courses' => Course::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.admin');
    }

    public function getAvailableTypesProperty(): array
    {
        if (!$this->topic_id) {
            return Material::TYPES;
        }

        $usedTypes = Material::query()
            ->where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->distinct()
            ->pluck('type')
            ->toArray();

        $available = array_values(array_diff(Material::TYPES, $usedTypes));

        if ($this->editingId && $this->type &&!in_array($this->type, $available, true)) {
            $available[] = $this->type;
        }

        return $available;
    }

    public function getIsTopicFullProperty(): array
    {
        return Topic::query()
            ->withCount('materials')
            ->get()
            ->mapWithKeys(fn ($topic) => [$topic->id => $topic->materials_count >= 3])
            ->all();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'topic_id',
            'name',
            'materialFile',
            'external_url',
            'type',
            'visibility',
            'status',
            'sort_order',
        ]);

        $this->visibility = 'public';
        $this->status = 'active';
        $this->sort_order = 0;
        $this->type = null;
        $this->showModal = false;
        $this->uploading = false;
    }
}