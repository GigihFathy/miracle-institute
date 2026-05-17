<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Material;
use App\Models\Topic;
use App\Services\Materials\MaterialAssetService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaterialsTab extends Component
{
    use InteractsWithMentorTopic;
    use WithFileUploads;

    public Topic $topic;

    public ?string $selectedMaterialId = null;
    public ?string $editingMaterialId = null;

    public bool $showMaterialModal = false;

    public string $materialName = '';
    public string $materialType = 'pdf';
    public string $materialStatus = 'active';
    public string $materialExternalUrl = '';
    public mixed $materialFile = null;
    public int $materialSortOrder = 1;

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $this->selectedMaterialId = $this->materialsQuery()->value('id');
        $this->materialSortOrder = ($this->topic->materials()->max('sort_order') ?? 0) + 1;
    }

    // Fungsi Lifecycle: Membersihkan input saat Type diubah
    public function updatedMaterialType(): void
    {
        $this->materialFile = null;
        $this->materialExternalUrl = '';
        $this->resetErrorBag(['materialFile', 'materialExternalUrl']);
    }

    public function openMaterialModal(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        if ($this->topic->materials()->count() >= 5) {
            session()->flash('error', 'Batas material per topic sudah penuh.');
            return;
        }

        $this->resetMaterialForm();
        $this->showMaterialModal = true;
    }

    public function editMaterial(string $id): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $material = Material::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($id);

        $this->editingMaterialId = $material->id;
        $this->materialName = $material->name;
        $this->materialType = $material->type;
        $this->materialStatus = $material->status;
        $this->materialExternalUrl = $material->external_url ?? '';
        $this->materialFile = null;
        $this->materialSortOrder = $material->sort_order;

        $this->showMaterialModal = true;
    }

    public function closeMaterialModal(): void
    {
        $this->showMaterialModal = false;
    }

    public function selectMaterial(string $materialId): void
    {
        $exists = $this->materialsQuery()->whereKey($materialId)->exists();

        if ($exists) {
            $this->selectedMaterialId = $materialId;
        }
    }

    private function availableMaterialTypes(): array
    {
        // Allow any type to be added (user requested ability to add multiple of same type)
        return ['pdf', 'ppt', 'video'];
    }

    public function saveMaterial(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $availableTypes = $this->availableMaterialTypes();

        $this->validate([
            'materialName' => ['required', 'string', 'max:255'],
            'materialType' => ['required', Rule::in($availableTypes)],
            'materialStatus' => ['required', Rule::in(Material::STATUSES)],
            'materialSortOrder' => ['required', 'integer', 'min:0'],
            'materialExternalUrl' => ['nullable', 'url', 'max:2048'],
            'materialFile' => ['nullable', 'file', 'max:51200'],
        ]);

        $existing = $this->editingMaterialId
            ? Material::query()->where('topic_id', $this->topic->id)->findOrFail($this->editingMaterialId)
            : null;

        if ($this->materialType === 'video' &&!$this->materialExternalUrl &&!$this->materialFile &&!($existing?->external_url)) {
            throw ValidationException::withMessages([
                'materialExternalUrl' => 'Video wajib memakai URL YouTube.',
            ]);
        }

        if (in_array($this->materialType, ['pdf', 'ppt'], true) &&!$this->materialFile &&!($existing?->path)) {
            throw ValidationException::withMessages([
                'materialFile' => 'File PDF/PPT wajib diunggah.',
            ]);
        }

        $isEditing = (bool) $this->editingMaterialId;

        DB::transaction(function () use ($existing, &$material) {
            $asset = app(MaterialAssetService::class)->sync(
                material: $existing,
                type: $this->materialType,
                file: $this->materialFile,
                externalUrl: $this->materialExternalUrl ?: null,
                title: $this->materialName
            );

            $payload = [
                'topic_id' => $this->topic->id,
                'uploader_id' => auth()->id(),
                'name' => $this->materialName,
                'type' => $this->materialType,
                'path' => $asset['path'],
                'external_url' => $asset['external_url'],
                'visibility' => 'public',
                'sort_order' => $this->materialSortOrder,
                'status' => $this->materialStatus,
            ];

            if ($existing) {
                $existing->update($payload);
                $material = $existing;
                return;
            }

            $material = Material::create($payload);
        });

        $this->selectedMaterialId = $material->id;
        $this->closeMaterialModal();
        $this->resetMaterialForm();

        session()->flash('success', $isEditing ? 'Material berhasil diperbarui.' : 'Material berhasil ditambahkan.');
    }

    public function deleteMaterial(string $id): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $material = Material::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($id);

        DB::transaction(function () use ($material) {
            app(MaterialAssetService::class)->delete($material);
            $material->delete();

            // Reorder remaining materials' sort_order sequentially starting from 1
            $remaining = Material::query()
                ->where('topic_id', $this->topic->id)
                ->orderBy('sort_order')
                ->orderBy('created_at')
                ->get();

            foreach ($remaining as $index => $m) {
                $newOrder = $index + 1;
                if ($m->sort_order !== $newOrder) {
                    $m->sort_order = $newOrder;
                    $m->save();
                }
            }
        });

        $this->selectedMaterialId = Material::query()
            ->where('topic_id', $this->topic->id)
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->value('id');

        session()->flash('success', 'Material berhasil dihapus.');
    }

    private function resetMaterialForm(): void
    {
        $this->reset([
            'editingMaterialId',
            'materialName',
            'materialType',
            'materialStatus',
            'materialExternalUrl',
            'materialFile',
            'materialSortOrder',
        ]);

        $this->materialType = 'pdf';
        $this->materialStatus = 'active';
        $this->materialSortOrder = ($this->topic->materials()->max('sort_order') ?? 0) + 1;
    }

    public function render()
    {
        $materials = $this->materialsQuery()->get();

        $selectedMaterial = $this->selectedMaterialId
            ? $materials->firstWhere('id', $this->selectedMaterialId)
            : $materials->first();

        if (!$selectedMaterial && $materials->isNotEmpty()) {
            $selectedMaterial = $materials->first();
            $this->selectedMaterialId = $selectedMaterial->id;
        }

        return view('livewire.mentor.topics.tabs.materials-tab', [
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'materialPreviewUrl' => app(MaterialAssetService::class)->resolvePreviewUrl($selectedMaterial),
            'materialTypeOptions' => $this->availableMaterialTypes(),
            'canAddMaterial' => $materials->count() < 5,
        ]);
    }

    private function materialsQuery()
    {
        return $this->topic->materials()
            ->with('uploader')
            ->orderBy('sort_order')
            ->orderBy('created_at');
    }
}