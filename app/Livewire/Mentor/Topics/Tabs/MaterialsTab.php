<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Material;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MaterialsTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public ?string $selectedMaterialId = null;
    public ?string $editingMaterialId = null;

    public bool $showMaterialModal = false;

    public string $materialName = '';
    public string $materialType = 'pdf';
    public string $materialStatus = 'active';
    public ?string $materialExternalUrl = null;
    public int $materialSortOrder = 1;

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $this->selectedMaterialId = $this->topic->materials()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->value('id');

        $this->materialSortOrder = ($this->topic->materials()->max('sort_order') ?? 0) + 1;
    }

    public function openMaterialModal(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        if ($this->topic->materials()->count() >= 3) {
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
        $this->materialExternalUrl = $material->external_url;
        $this->materialSortOrder = $material->sort_order;

        $this->showMaterialModal = true;
    }

    public function closeMaterialModal(): void
    {
        $this->showMaterialModal = false;
    }

    public function selectMaterial(string $materialId): void
    {
        $this->selectedMaterialId = $materialId;
    }

    private function availableMaterialTypes(): array
    {
        $all = ['pdf', 'ptt', 'video'];

        $materials = Material::query()
            ->where('topic_id', $this->topic->id)
            ->get(['id', 'type']);

        $used = $materials->pluck('type')->unique()->values()->all();

        if ($this->editingMaterialId) {
            $current = $materials->firstWhere('id', $this->editingMaterialId)?->type;
            $available = array_values(array_diff($all, array_diff($used, [$current])));

            return array_values(array_unique($available));
        }

        return array_values(array_diff($all, $used));
    }

    public function saveMaterial(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $availableTypes = $this->availableMaterialTypes();

        $this->validate([
            'materialName' => ['required', 'string', 'max:255'],
            'materialType' => ['required', Rule::in($availableTypes)],
            'materialStatus' => ['required', Rule::in(['active', 'inactive'])],
            'materialSortOrder' => ['required', 'integer', 'min:1'],
            'materialExternalUrl' => ['nullable', 'url', 'max:2048'],
        ]);

        $material = $this->editingMaterialId
            ? Material::query()->where('topic_id', $this->topic->id)->findOrFail($this->editingMaterialId)
            : null;

        if ($this->materialType === 'video' && ! $this->materialExternalUrl) {
            throw ValidationException::withMessages([
                'materialExternalUrl' => 'Video wajib memakai external path.',
            ]);
        }

        DB::transaction(function () use ($material) {
            $path = $material?->path;

            if ($this->materialType === 'video') {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
                $path = null;
            }

            $payload = [
                'topic_id' => $this->topic->id,
                'uploader_id' => auth()->id(),
                'name' => $this->materialName,
                'type' => $this->materialType,
                'path' => $path,
                'external_url' => $this->materialType === 'video' ? $this->materialExternalUrl : null,
                'visibility' => 'public',
                'sort_order' => $this->materialSortOrder,
                'status' => $this->materialStatus,
            ];

            if ($material) {
                $material->update($payload);
                $this->selectedMaterialId = $material->id;
                return;
            }

            $created = Material::create($payload);
            $this->selectedMaterialId = $created->id;
        });

        $this->closeMaterialModal();
        $this->resetMaterialForm();

        session()->flash('success', $this->editingMaterialId ? 'Material berhasil diperbarui.' : 'Material berhasil ditambahkan.');
    }

    public function deleteMaterial(string $id): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_materials', 'manage_topics']), 403);

        $material = Material::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($id);

        DB::transaction(function () use ($material) {
            if ($material->path) {
                Storage::disk('public')->delete($material->path);
            }

            $material->delete();
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
            'materialSortOrder',
        ]);

        $this->materialType = 'pdf';
        $this->materialStatus = 'active';
        $this->materialSortOrder = ($this->topic->materials()->max('sort_order') ?? 0) + 1;
    }

    public function render()
    {
        $materials = $this->topic->materials()
            ->with('uploader')
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        $selectedMaterial = $this->selectedMaterialId
            ? $materials->firstWhere('id', $this->selectedMaterialId)
            : $materials->first();

        if (! $selectedMaterial && $materials->isNotEmpty()) {
            $selectedMaterial = $materials->first();
            $this->selectedMaterialId = $selectedMaterial->id;
        }

        return view('livewire.mentor.topics.tabs.materials-tab', [
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'materialPreviewUrl' => $selectedMaterial
                ? ($selectedMaterial->external_url ?: ($selectedMaterial->path ? Storage::disk('public')->url($selectedMaterial->path) : null))
                : null,
            'materialTypeOptions' => $this->availableMaterialTypes(),
            'canAddMaterial' => $materials->count() < 3,
        ]);
    }
}