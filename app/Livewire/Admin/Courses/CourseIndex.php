<?php

namespace App\Livewire\Admin\Courses;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Support\Str;
use Livewire\Component;

class CourseIndex extends Component
{
    use WithAdminTableState;

    public ?string $editingId = null;
    public string $study_program_id;
    public string $title;
    public string $slug;
    public string $poster;
    public int $credit;
    public int $quota;
    public string $description;
    public string $status = 'active';

    protected function rules(): array
    {
        return [
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'poster' => 'nullable|string|max:255',
            'credit' => 'required|integer|min:0',
            'quota' => 'required|integer|min:0',
            'description' => 'required|string',
            'status' => 'required|string|max:50',
        ];
    }

    public function updatedTitle($value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $row = Course::findOrFail($id);

        $this->editingId = $row->id;
        $this->study_program_id = $row->study_program_id;
        $this->title = $row->title;
        $this->slug = $row->slug;
        $this->poster = $row->poster;
        $this->credit = $row->credit;
        $this->quota = $row->quota;
        $this->description = $row->description;
        $this->status = $row->status;
    }

    public function save(): void
    {
        $this->validate();

        Course::updateOrCreate(
            ['id' => $this->editingId],
            [
                'study_program_id' => $this->study_program_id,
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'poster' => $this->poster,
                'credit' => $this->credit,
                'quota' => $this->quota,
                'description' => $this->description,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
    }

    public function delete(string $id): void
    {
        Course::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.courses.index', [
            'rows' => Course::with('studyProgram')
                ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'study_program_id', 'title', 'slug', 'poster', 'credit', 'quota', 'description', 'status']);
        $this->status = 'active';
    }
}