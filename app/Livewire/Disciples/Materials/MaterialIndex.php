<?php

namespace App\Livewire\Disciples\Materials;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Livewire\Component;
use Throwable;

class MaterialIndex extends Component
{
    use WithTableState;
    use WithFileUploads;

    public array $openTopics = [];

    public bool $showModal = false;
    public ?string $editingId = null;
    public ?string $selectedTopicId = null;
    public bool $isSaving = false;

    public string $topic_id = '';
    public string $uploader_id = '';
    public string $name = '';
    public string $visibility = 'Public';
    public string $path = '';
    public string $external_url = '';
    public string $type = 'pdf';
    public string $status = 'active';
    public int $sort_order = 0;

    public $uploadFile = null;
    public ?string $currentPath = null;

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
        'perPage' => ['except' => 12],
    ];

    public function mount(): void
    {
        $this->syncSelectedTopicToFirstAvailable();
    }

    protected function rules(): array
    {
        return [
            'topic_id' => 'required|exists:topics,id',
            'uploader_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:255',
            'uploadFile' => 'nullable|file|max:51200',
            'type' => 'required|in:pdf,ppt,video',
            'visibility' => 'required|in:Public,Private',
            'status' => 'required|in:active,inactive,draft',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedTopicFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function setTypeTab(string $type = ''): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedVisibilityFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
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
            $this->selectedTopicId = $topicId;
            $this->openTopics = array_values(array_unique([...$this->openTopics, $topicId]));
        }

        $this->uploader_id = auth()->id() ?? '';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Material::findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->uploader_id = $row->uploader_id;
        $this->name = $row->name;
        $this->path = $row->path ?? '';
        $this->currentPath = $row->path ?? null;
        $this->external_url = $row->external_url ?? '';
        $this->type = $row->type;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);

        $this->selectedTopicId = $row->topic_id;
        $this->openTopics = array_values(array_unique([...$this->openTopics, $row->topic_id]));
        $this->showModal = true;
    }

    public function selectTopic(string $id): void
    {
        $this->selectedTopicId = $id;
    }

    public function resolveOpenUrl(?string $externalUrl, ?string $path): ?string
    {
        if (! empty($externalUrl)) {
            return $externalUrl;
        }

        if (empty($path)) {
            return null;
        }

        try {
            return Storage::disk('public')->url($path);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            $this->validate();
            $this->validateMaterialType();

            if (! $this->editingId && ! $this->uploadFile && ! $this->path && ! $this->external_url) {
                throw ValidationException::withMessages([
                    'path' => 'Upload file, file path, atau external URL wajib diisi.',
                ]);
            }

            $count = Material::where('topic_id', $this->topic_id)
                ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
                ->count();

            if (! $this->editingId && $count >= 3) {
                throw ValidationException::withMessages([
                    'topic_id' => 'Setiap topic hanya boleh memiliki 3 material.',
                ]);
            }

            $storedPath = $this->currentPath;

            if ($this->uploadFile) {
                if ($storedPath && $this->safeFileExists($storedPath)) {
                    Storage::disk('public')->delete($storedPath);
                }

                $storedPath = $this->uploadFile->store('materials', 'public');
            }

            if (! $storedPath && $this->path) {
                $storedPath = $this->path;
            }

            Material::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'topic_id' => $this->topic_id,
                    'uploader_id' => $this->uploader_id,
                    'name' => $this->name,
                    'path' => $storedPath,
                    'external_url' => $this->external_url ?: null,
                    'type' => $this->type,
                    'visibility' => $this->visibility,
                    'status' => $this->status,
                    'sort_order' => $this->sort_order,
                ]
            );

            $this->resetForm();
            $this->showModal = false;

            $this->syncSelectedTopicToFirstAvailable();

            session()->flash('success', 'Material berhasil disimpan.');
        } finally {
            $this->isSaving = false;
        }
    }

    public function delete(string $id): void
    {
        Material::findOrFail($id)->delete();

        if ($this->selectedTopicId) {
            $this->selectedTopicId = Topic::where('id', $this->selectedTopicId)->value('id')
                ?: $this->syncSelectedTopicIdAfterDelete();
        }

        session()->flash('success', 'Material berhasil dihapus.');
    }

    public function render()
    {
        $topicsQuery = $this->baseTopicQuery();

        $topics = $topicsQuery
            ->with([
                'course',
                'teacher',
                'materials' => function ($q) {
                    $q->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
                        ->when($this->visibilityFilter, fn ($q) => $q->where('visibility', $this->visibilityFilter))
                        ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                        ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orderBy('sort_order')
                        ->orderBy('name');
                },
            ])
            ->withCount(['materials', 'videoSessions', 'certificates'])
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        $selectedTopic = $this->selectedTopicId
            ? Topic::with(['course.studyProgram', 'teacher', 'materials', 'videoSessions', 'certificates'])->find($this->selectedTopicId)
            : null;

        $topicsInScope = $this->baseTopicQuery()->count();
        $materialsInScope = Material::whereHas('topic', fn ($q) => $this->applyTopicFilters($q))->count();

        $typeCounts = Material::whereHas('topic', fn ($q) => $this->applyTopicFilters($q))
            ->select('type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $totalBytes = 0;
        $durationMinutes = 0;

        $scopedMaterials = Material::whereHas('topic', fn ($q) => $this->applyTopicFilters($q))->get();
        foreach ($scopedMaterials as $material) {
            $size = $this->resolveStoredSize($material->path);
            if ($size !== null) {
                $totalBytes += $size;
                $durationMinutes += $this->estimateReadTimeMinutes($size, $material->type);
            }
        }

        $statsCards = [
            [
                'label' => 'Topics in Scope',
                'value' => number_format($topicsInScope),
                'note' => 'Topic yang cocok dengan filter aktif.',
            ],
            [
                'label' => 'Materials Total',
                'value' => number_format($materialsInScope),
                'note' => 'Seluruh material dalam scope yang ditampilkan.',
            ],
            [
                'label' => 'PDF / PPT / Video',
                'value' => ($typeCounts['pdf'] ?? 0) . ' / ' . ($typeCounts['ppt'] ?? 0) . ' / ' . ($typeCounts['video'] ?? 0),
                'note' => 'Komposisi tipe file dalam scope.',
            ],
            [
                'label' => 'Total File Size',
                'value' => $this->formatBytes($totalBytes),
                'note' => 'Ukuran gabungan file lokal yang tersedia.',
            ],
            [
                'label' => 'Estimated Read Time',
                'value' => $durationMinutes > 0 ? $this->formatMinutes($durationMinutes) : 'N/A',
                'note' => 'Estimasi waktu baca dari file yang tersedia.',
            ],
            [
                'label' => 'Per Topic Max',
                'value' => '3',
                'note' => 'Batas material per topic tetap dijaga.',
            ],
        ];

        if ($selectedTopic) {
            $selectedTopic->setAttribute('total_material_size', $this->formatBytes(
                $selectedTopic->materials->sum(fn ($material) => $this->resolveStoredSize($material->path) ?? 0)
            ));
            $selectedTopic->setAttribute('estimated_read_time', $this->formatMinutes(
                $selectedTopic->materials->sum(fn ($material) => $this->resolveStoredSize($material->path) !== null
                    ? $this->estimateReadTimeMinutes($this->resolveStoredSize($material->path), $material->type)
                    : 0)
            ));
            $selectedTopic->setAttribute('assessment_label', $selectedTopic->course?->assessment
                ? ucfirst($selectedTopic->course->assessment->status)
                : 'No assessment');
            $selectedTopic->setAttribute('assessment_badge_class', $this->assessmentBadgeClass($selectedTopic->course?->assessment?->status));
        }

        return view('livewire.disciples.materials.index', [
            'topics' => $topics,
            'selectedTopic' => $selectedTopic,
            'courses' => Course::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
            'statsCards' => $statsCards,
        ])->layout('layouts.learning');
    }

    private function baseTopicQuery()
    {
        return Topic::query()
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('teacher', fn ($tq) => $tq->where(function ($teacherQuery) {
                            $teacherQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('full_name', 'like', '%' . $this->search . '%');
                        }));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->topicFilter, fn ($q) => $q->where('id', $this->topicFilter));
    }

    private function applyTopicFilters($query): void
    {
        $query->when($this->search, function ($q) {
            $q->where(function ($inner) {
                $inner->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('teacher', fn ($tq) => $tq->where(function ($teacherQuery) {
                        $teacherQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('full_name', 'like', '%' . $this->search . '%');
                    }));
            });
        })
        ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
        ->when($this->topicFilter, fn ($q) => $q->where('id', $this->topicFilter));
    }

    private function syncSelectedTopicToFirstAvailable(): void
    {
        $this->selectedTopicId = $this->baseTopicQuery()
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->value('id');
    }

    private function syncSelectedTopicIdAfterDelete(): ?string
    {
        $next = $this->baseTopicQuery()
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->value('id');

        $this->selectedTopicId = $next;

        return $next;
    }

    private function validateMaterialType(): void
    {
        if (! $this->topic_id || ! $this->type) {
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

    private function resolveStoredSize(?string $path): ?int
    {
        if (! $path) {
            return null;
        }

        if (! $this->safeFileExists($path)) {
            return null;
        }

        try {
            return Storage::disk('public')->size($path);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    private function safeFileExists(string $path): bool
    {
        try {
            return Storage::disk('public')->exists($path);
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }

    private function estimateReadTimeMinutes(int $bytes, string $type): int
    {
        $mb = max(0.1, $bytes / 1024 / 1024);

        return match ($type) {
            'video' => (int) max(1, ceil($mb * 1.5)),
            'ppt' => (int) max(1, ceil($mb * 2.2)),
            default => (int) max(1, ceil($mb * 3)),
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 1) . ' ' . $units[$power];
    }

    private function formatMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return 'N/A';
        }

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

    private function assessmentBadgeClass(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
            'inactive' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-500 border-slate-200',
        };
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
            'uploadFile',
            'type',
            'visibility',
            'status',
            'sort_order',
            'currentPath',
        ]);

        $this->visibility = 'Public';
        $this->type = 'pdf';
        $this->status = 'active';
        $this->sort_order = 0;
    }
}