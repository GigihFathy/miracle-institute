<?php

namespace App\Livewire\Mentor\Topics;

use App\Models\Assessment;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TopicWorkspace extends Component
{
    use WithFileUploads;

    public Topic $topic;

    public string $tab = 'overview';
    public ?string $selectedMaterialId = null;

    public string $materialName = '';
    public string $materialType = 'pdf';
    public string $materialVisibility = 'Public';
    public string $materialStatus = 'active';
    public ?string $materialExternalUrl = null;
    public int $materialSortOrder = 0;
    public $materialFile;

    public function mount(string $slug): void
    {
        $this->topic = Topic::with([
            'course',
            'materials.uploader',
            'sessions',
            'assessments.questions',
        ])->where('slug', $slug)->firstOrFail();

        abort_unless(
            $this->topic->teacher_id === auth()->id() || auth()->user()->can('manage_topics'),
            403
        );

        $this->selectedMaterialId = $this->topic->materials->first()?->id;

        $this->materialSortOrder = ($this->topic->materials->max('sort_order') ?? 0) + 1;
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function selectMaterial(string $materialId): void
    {
        $this->selectedMaterialId = $materialId;
        $this->tab = 'materials';
    }

    public function saveMaterial(): void
    {
        $this->validate([
            'materialName' => ['required', 'string', 'max:255'],
            'materialType' => ['required', Rule::in(['pdf', 'video', 'doc', 'ppt', 'audio', 'image', 'link'])],
            'materialVisibility' => ['required', Rule::in(['Public', 'Private'])],
            'materialStatus' => ['required', Rule::in(['active', 'inactive', 'draft'])],
            'materialSortOrder' => ['required', 'integer', 'min:0'],
            'materialExternalUrl' => ['nullable', 'url'],
            'materialFile' => ['nullable', 'file', 'max:51200'],
        ]);

        if (! $this->materialFile && ! $this->materialExternalUrl) {
            $this->addError('materialFile', 'Upload file atau isi external URL.');
            return;
        }

        $path = null;

        if ($this->materialFile) {
            $path = $this->materialFile->store('materials/' . $this->topic->id, 'public');
        }

        $material = Material::create([
            'topic_id' => $this->topic->id,
            'uploader_id' => auth()->id(),
            'name' => $this->materialName,
            'visibility' => $this->materialVisibility,
            'path' => $path,
            'external_url' => $this->materialExternalUrl,
            'type' => $this->materialType,
            'status' => $this->materialStatus,
            'sort_order' => $this->materialSortOrder,
        ]);

        $this->resetMaterialForm();
        $this->selectedMaterialId = $material->id;
        session()->flash('success', 'Material berhasil ditambahkan.');
    }

    public function deleteMaterial(string $id): void
    {
        $material = Material::where('topic_id', $this->topic->id)->findOrFail($id);

        if ($material->path) {
            Storage::disk('public')->delete($material->path);
        }

        $material->delete();

        if ($this->selectedMaterialId === $id) {
            $this->selectedMaterialId = $this->topic->materials()->latest()->value('id');
        }

        session()->flash('success', 'Material berhasil dihapus.');
    }

    private function resetMaterialForm(): void
    {
        $this->reset([
            'materialName',
            'materialType',
            'materialVisibility',
            'materialStatus',
            'materialExternalUrl',
            'materialSortOrder',
            'materialFile',
        ]);

        $this->materialType = 'pdf';
        $this->materialVisibility = 'Public';
        $this->materialStatus = 'active';
        $this->materialSortOrder = ($this->topic->materials()->max('sort_order') ?? 0) + 1;
    }

    public function render()
    {
        $materials = $this->topic->materials()->with('uploader')->latest()->get();

        $selectedMaterial = $this->selectedMaterialId
            ? $materials->firstWhere('id', $this->selectedMaterialId)
            : $materials->first();

        $students = CourseEnrollment::with('user')
            ->where('course_id', $this->topic->course_id)
            ->get()
            ->map(function ($enrollment) {
                $progress = TopicProgress::where('course_enrollment_id', $enrollment->id)
                    ->where('topic_id', $this->topic->id)
                    ->first();

                $status = $progress->status ?? 'not_started';

                $percent = match ($status) {
                    'completed' => 100,
                    'in_progress' => 50,
                    default => 0,
                };

                return [
                    'enrollment' => $enrollment,
                    'progress' => $progress,
                    'status' => $status,
                    'percent' => $percent,
                ];
            });

        $assessment = $this->topic->assessments->first();

        return view('livewire.mentor.topics.topic-workspace', [
            'materials' => $materials,
            'selectedMaterial' => $selectedMaterial,
            'students' => $students,
            'assessment' => $assessment,
            'materialPreviewUrl' => $selectedMaterial
                ? ($selectedMaterial->external_url ?: ($selectedMaterial->path ? Storage::disk('public')->url($selectedMaterial->path) : null))
                : null,
        ])->layout('layouts.learning');
    }
}