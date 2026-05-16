<?php

namespace App\Livewire\Admin\Topics;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use App\Models\VideoSession;
use Illuminate\Support\Str;
use Livewire\Component;

class TopicIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;

    public ?string $editingId = null;
    public string $course_id = '';
    public string $teacher_id = '';
    public string $name = '';
    public string $description = '';
    public string $visibility = 'Public';
    public string $status = 'active';
    public int $sort_order = 0;

    public string $courseFilter = '';
    public string $teacherFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'teacherFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'visibility' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Topic::findOrFail($id);

        $this->editingId = $row->id;
        $this->course_id = $row->course_id;
        $this->teacher_id = $row->teacher_id;
        $this->name = $row->name;
        $this->description = $row->description;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $course = Course::with('studyProgram')->findOrFail($this->course_id);

        Topic::updateOrCreate(
            ['id' => $this->editingId],
            [
                'course_id' => $this->course_id,
                'teacher_id' => $this->teacher_id,
                'name' => $this->name,
                'category' => strtolower($course->studyProgram->title),
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        
        session()->flash('success', 'Topic berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Topic::findOrFail($id)->delete();
        session()->flash('success', 'Topic berhasil dihapus.');
    }

    public function render()
    {
        $rows = Topic::with(['course', 'teacher', 'course.certificates'])
            ->withCount([
                'materials',
                'videoSessions',
            ])
            ->with(['course.assessment'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('category', 'like', "%{$this->search}%");
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->teacherFilter, fn ($q) => $q->where('teacher_id', $this->teacherFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.topics.index', [
            'rows' => $rows,
            'courses' => Course::orderBy('title')->get(),
            'teachers' => User::whereHas('roles', fn ($q) => $q->where('name', 'disciples'))
                ->orderBy('name')
                ->get(),
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'stats' => [
                'courses' => Course::count(),
                'topics' => Topic::count(),
                'materials' => Material::count(),
                'sessions' => VideoSession::count(),
                'certificates' => Certificate::count(),
            ],
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'course_id',
            'teacher_id',
            'name',
            'description',
            'visibility',
            'status',
            'sort_order',
        ]);

        $this->visibility = 'Public';
        $this->status = 'active';
        $this->sort_order = 0;
    }
}