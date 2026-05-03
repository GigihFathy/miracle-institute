<?php

namespace App\Livewire\Admin\Topics;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;

class TopicIndex extends Component
{
    use WithAdminTableState;

    public ?string $editingId = null;
    public string $course_id;
    public string $teacher_id;
    public string $name;
    public string $category;
    public string $description;
    public string $poster;
    public string $visibility = 'Public';
    public string $status = 'active';
    public int $sort_order;

    protected function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string',
            'poster' => 'nullable|string|max:255',
            'visibility' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $row = Topic::findOrFail($id);

        $this->editingId = $row->id;
        $this->course_id = $row->course_id;
        $this->teacher_id = $row->teacher_id;
        $this->name = $row->name;
        $this->category = $row->category ?? '';
        $this->description = $row->description;
        $this->poster = $row->poster;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);
    }

    public function save(): void
    {
        $this->validate();

        $course = Course::with('studyProgram')->find($this->course_id);
        
        $categoryName = $course->studyProgram->title ?? $this->category;

        Topic::updateOrCreate(
            ['id' => $this->editingId],
            [
                'course_id' => $this->course_id,
                'teacher_id' => $this->teacher_id,
                'name' => $this->name,
                'category' => $categoryName,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'poster' => $this->poster,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ]
        );

        $this->resetForm();
    }

    public function delete(string $id): void
    {
        Topic::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.topics.index', [
            'rows' => Topic::with(['course', 'teacher'])
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
            'courses' => Course::orderBy('title')->get(),
            'teachers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'disciples');
            })
            ->orderBy('first_name')
            ->get(),
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'course_id', 'teacher_id', 'name', 'category', 'description', 'poster', 'visibility', 'status', 'sort_order']);
        $this->visibility = 'Public';
        $this->status = 'active';
    }
}