<?php

namespace App\Livewire\Admin\Materials;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Services\Materials\MaterialAssetService;
use App\Services\Materials\YoutubeService;
use Illuminate\Support\Facades\Auth;
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
        'typeFilter' => ['except' => ''],
        'visibilityFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount(?string $topicFilter = null): void
    {
        $this->showModal = false;
        $this->topicFilter = $topicFilter ?? '';
    }

    protected function rules(): array
    {
        return [
            'topic_id' => ['required', 'exists:topics,id'],
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', Rule::in(Material::TYPES)],
            'visibility' => ['required', Rule::in(Material::VISIBILITIES)],
            'status' => ['required', Rule::in(Material::STATUSES)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'materialFile' => ['nullable', 'file', 'mimes:pdf,ppt,pptx', 'max:51200'],
            'external_url' => [
                'nullable',
                Rule::requiredIf($this->type === 'video'),
                $this->type === 'video' ? 'url' : '',
                'max:2048'
            ],
        ];
    }

    public function updatedType(): void
    {
        $this->materialFile = null;
        $this->external_url = '';
    }

    public function updatedTopicId(): void
    {
        $this->updatedType();

        if ($this->editingId) {
            return;
        }

        $this->sort_order = $this->nextSortOrderForTopic($this->topic_id);
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

        $this->topic_id = $topicId ?: ($this->topicFilter ?: null);

        if ($this->topic_id && !in_array($this->topic_id, $this->openTopics, true)) {
            $this->openTopics[] = $this->topic_id;
        }

        $this->type = $this->availableTypes[0] ?? null;
        $this->sort_order = $this->nextSortOrderForTopic($this->topic_id);

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

        // max 5 materials per topic
        if (!$this->editingId) {
            $currentCount = Material::query()
                ->where('topic_id', $this->topic_id)
                ->count();
            if ($currentCount >= 5) {
                throw ValidationException::withMessages([
                    'topic_id' => 'Setiap topic hanya boleh memiliki maksimal 5 material.',
                ]);
            }
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
            return;
        }

        $payload = [
            'topic_id' => $this->topic_id,
            'uploader_id' => Auth::id(),
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
                    ->orderBy('sort_order')
                    ->orderBy('created_at'),
            ])
            ->when($this->topicFilter, fn ($q) => $q->where('id', $this->topicFilter))
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
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

        $selectedTopic = $this->topicFilter
            ? Topic::with(['course', 'materials' => fn ($q) => $q->orderBy('sort_order')->orderBy('created_at')])->find($this->topicFilter)
            : null;

        return view('livewire.admin.materials.index', [
            'topics' => $topics,
            'courses' => Course::orderBy('title')->get(),
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'selectedTopic' => $selectedTopic,
        ])->layout('layouts.admin');
    }

    public function getAvailableTypesProperty(): array
    {
        return Material::TYPES;
    }

    public function getIsTopicFullProperty(): array
    {
        return Topic::query()
            ->withCount('materials')
            ->get()
            ->mapWithKeys(fn ($topic) => [$topic->id => $topic->materials_count >= 5])
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
        $this->sort_order = 1;
        $this->type = null;
        $this->showModal = false;
    }

    private function nextSortOrderForTopic(?string $topicId): int
    {
        if (!$topicId) {
            return 1;
        }

        $lastSortOrder = Material::query()
            ->where('topic_id', $topicId)
            ->max('sort_order');

        return max(1, ((int) $lastSortOrder) + 1);
    }
}
