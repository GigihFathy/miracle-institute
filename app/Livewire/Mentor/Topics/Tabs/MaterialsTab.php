<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Material;
use App\Models\Topic;
use App\Services\Materials\MaterialAssetService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;

class MaterialsTab extends Component
{
    use InteractsWithMentorTopic;
    use WithFileUploads;

    public string $topicId;

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
        $this->topicId = $topicId;

        $topic = $this->topic();

        abort_unless(
            $this->canAccessTopic($topic, ['manage_materials', 'manage_topics']),
            403
        );

        $this->selectedMaterialId = $this->materialsQuery()->value('id');
        $this->materialSortOrder = ($topic->materials()->max('sort_order') ?? 0) + 1;
    }

    private function topic(): Topic
    {
        return $this->loadTopic($this->topicId);
    }

    public function updatedMaterialType(): void
    {
        $this->materialFile = null;
        $this->materialExternalUrl = '';
        $this->resetErrorBag(['materialFile', 'materialExternalUrl']);
    }

    public function openMaterialModal(): void
    {
        $topic = $this->topic();

        abort_unless(
            $this->canAccessTopic($topic, ['manage_materials', 'manage_topics']),
            403
        );

        if ($topic->materials()->count() >= 5) {
            session()->flash('error', 'Batas material per topic sudah penuh.');
            return;
        }

        $this->resetMaterialForm();
        $this->showMaterialModal = true;
    }

    public function editMaterial(string $id): void
    {
        $topic = $this->topic();

        abort_unless(
            $this->canAccessTopic($topic, ['manage_materials', 'manage_topics']),
            403
        );

        $material = Material::query()
            ->where('topic_id', $topic->id)
            ->findOrFail($id);

        $this->editingMaterialId = $material->id;
        $this->materialName = $material->name;
        $this->materialType = $material->type;
        $this->materialStatus = $material->status;
        $this->materialExternalUrl = $material->external_url ?? '';
        $this->materialFile = null;
        $this->materialSortOrder = (int) ($material->sort_order ?? 1);

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
        return ['pdf', 'ppt', 'video'];
    }

    public function saveMaterial(): void
    {
        $topic = $this->topic();

        abort_unless(
            $this->canAccessTopic($topic, ['manage_materials', 'manage_topics']),
            403
        );

        $this->validate([
            'materialName' => ['required', 'string', 'max:50'],
            'materialType' => ['required', Rule::in($this->availableMaterialTypes())],
            'materialStatus' => ['required', Rule::in(Material::STATUSES)],
            'materialSortOrder' => ['required', 'integer', 'min:0'],
            'materialExternalUrl' => ['nullable', 'url', 'max:2048'],
            'materialFile' => ['nullable', 'file', 'mimes:pdf,ppt,pptx', 'max:51200'],
        ]);

        $existing = $this->editingMaterialId
            ? Material::query()
                ->where('topic_id', $topic->id)
                ->findOrFail($this->editingMaterialId)
            : null;

        $hasUpload = $this->hasUpload($this->materialFile);

        if ($this->materialType === 'video') {
            if ($hasUpload) {
                throw ValidationException::withMessages([
                    'materialFile' => 'Video tidak menerima upload file. Gunakan URL video.',
                ]);
            }

            if (!$this->materialExternalUrl && !($existing?->external_url)) {
                throw ValidationException::withMessages([
                    'materialExternalUrl' => 'Video wajib memakai URL.',
                ]);
            }
        }

        if (in_array($this->materialType, ['pdf', 'ppt'], true)) {
            if (!$hasUpload && !($existing?->path)) {
                throw ValidationException::withMessages([
                    'materialFile' => 'File PDF/PPT wajib diunggah.',
                ]);
            }
        }

        try {
            DB::transaction(function () use ($existing, $topic): void {
                $asset = app(MaterialAssetService::class)->sync(
                    material: $existing,
                    type: $this->materialType,
                    file: $this->materialFile,
                    externalUrl: $this->materialExternalUrl ?: null,
                    title: $this->materialName
                );

                $payload = [
                    'topic_id' => $topic->id,
                    'uploader_id' => Auth::id(),
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
                } else {
                    Material::create($payload);
                }
            });

            $this->selectedMaterialId = Material::query()
                ->where('topic_id', $topic->id)
                ->orderBy('sort_order')
                ->orderBy('created_at')
                ->value('id');

            $this->closeMaterialModal();
            $this->resetMaterialForm();

            session()->flash(
                'success',
                $this->editingMaterialId ? 'Material berhasil diperbarui.' : 'Material berhasil ditambahkan.'
            );
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            session()->flash('error', 'Terjadi kesalahan sistem saat menyimpan material.');
        }
    }

    public function deleteMaterial(string $id): void
    {
        $topic = $this->topic();

        abort_unless(
            $this->canAccessTopic($topic, ['manage_materials', 'manage_topics']),
            403
        );

        $material = Material::query()
            ->where('topic_id', $topic->id)
            ->findOrFail($id);

        try {
            DB::transaction(function () use ($material, $topic): void {
                if (in_array($material->type, ['pdf', 'ppt'], true)) {
                    app(MaterialAssetService::class)->delete($material);
                }

                $material->delete();

                $remaining = Material::query()
                    ->where('topic_id', $topic->id)
                    ->orderBy('sort_order')
                    ->orderBy('created_at')
                    ->get();

                foreach ($remaining as $index => $row) {
                    $newOrder = $index + 1;

                    if ((int) $row->sort_order !== $newOrder) {
                        $row->sort_order = $newOrder;
                        $row->save();
                    }
                }
            });

            $this->selectedMaterialId = Material::query()
                ->where('topic_id', $topic->id)
                ->orderBy('sort_order')
                ->orderBy('created_at')
                ->value('id');

            session()->flash('success', 'Material berhasil dihapus.');
        } catch (RuntimeException $e) {
            session()->flash('error', 'Gagal menghapus file dari cloud: ' . $e->getMessage());
        } catch (Exception $e) {
            report($e);
            session()->flash('error', 'Terjadi kesalahan sistem saat menghapus material.');
        }
    }

    private function resetMaterialForm(): void
    {
        $topic = $this->topic();

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
        $this->materialSortOrder = ($topic->materials()->max('sort_order') ?? 0) + 1;
    }

    public function render()
    {
        $topic = $this->topic();

        $materials = $this->materialsQuery()->get();

        $selectedMaterial = $this->selectedMaterialId
            ? $materials->firstWhere('id', $this->selectedMaterialId)
            : $materials->first();

        if (!$selectedMaterial && $materials->isNotEmpty()) {
            $selectedMaterial = $materials->first();
            $this->selectedMaterialId = $selectedMaterial->id;
        }

        return view('livewire.mentor.topics.tabs.materials-tab', [
            'topic' => $topic,
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'materialPreviewUrl' => app(MaterialAssetService::class)->resolvePreviewUrl($selectedMaterial),
            'materialTypeOptions' => $this->availableMaterialTypes(),
            'canAddMaterial' => $materials->count() < 5,
            'videoEmbedUrl' => $selectedMaterial?->external_url ? $this->getEmbedUrl($selectedMaterial->external_url) : null,
            'videoThumbnailUrl' => $selectedMaterial?->external_url ? $this->getThumbnailUrl($selectedMaterial->external_url) : null,
        ]);
    }

    private function materialsQuery()
    {
        return $this->topic()->materials()
            ->with('uploader')
            ->orderBy('sort_order')
            ->orderBy('created_at');
    }

    public function getEmbedUrl(string $url): string
    {
        $id = $this->extractVideoId($url);

        return $id ? 'https://www.youtube.com/embed/' . $id . '?playsinline=1&rel=0&modestbranding=1' : '';
    }

    public function getThumbnailUrl(string $url): ?string
    {
        $id = $this->extractVideoId($url);

        return $id ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg' : null;
    }

    private function extractVideoId(string $input): ?string
    {
        $input = trim(html_entity_decode($input));

        if ($input === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }

        $parts = parse_url($input);

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);

            if (! empty($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', $query['v'])) {
                return $query['v'];
            }
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if ($path !== '') {
            $segments = explode('/', $path);

            if (str_contains($host, 'youtu.be') && isset($segments[0]) && preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[0])) {
                return $segments[0];
            }

            foreach (['embed', 'shorts', 'live'] as $prefix) {
                $index = array_search($prefix, $segments, true);

                if ($index !== false && isset($segments[$index + 1]) && preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[$index + 1])) {
                    return $segments[$index + 1];
                }
            }
        }

        $patterns = [
            '/v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/embed\/([A-Za-z0-9_-]{11})/',
            '/shorts\/([A-Za-z0-9_-]{11})/',
            '/live\/([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function hasUpload(mixed $file): bool
    {
        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        return $file instanceof \Illuminate\Http\UploadedFile
            || $file instanceof TemporaryUploadedFile;
    }
}
