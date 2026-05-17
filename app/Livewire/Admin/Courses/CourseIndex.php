<?php

namespace App\Livewire\Admin\Courses;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Material;
use App\Models\StudyProgram;
use App\Models\Topic;
use App\Models\VideoSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class CourseIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;

    /** @var array<string> */
    public array $thumbnails = [];

    public ?string $editingId = null;
    public string $study_program_id = '';
    public string $title = '';
    public string $slug = '';
    public string $poster = '';
    public string $description = '';
    public string $status = 'active';

    public string $studyProgramFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'studyProgramFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'study_program_id' => 'required|exists:study_programs,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'poster' => 'nullable|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|max:50',
        ];
    }

    public function updatedTitle($value): void
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Course::findOrFail($id);

        $this->editingId = $row->id;
        $this->study_program_id = $row->study_program_id;
        $this->title = $row->title;
        $this->slug = $row->slug;
        $this->poster = $row->poster;
        $this->description = $row->description;
        $this->status = $row->status;

        $this->showModal = true;
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
                'description' => $this->description,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Course berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Course::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Course berhasil dihapus.');
    }

    public function mount(): void
    {
        $dir = public_path('images/thumbnail');

        if (File::exists($dir)) {
            $files = File::files($dir);
            $this->thumbnails = array_map(fn ($f) => 'images/thumbnail/' . $f->getFilename(), $files);
        }
    }

    public function selectThumbnail(string $path): void
    {
        $this->poster = $path;
    }

    public function render()
    {
        $rows = Course::with('studyProgram')
            ->withCount(['topics', 'enrollments', 'certificates'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->studyProgramFilter, fn ($q) => $q->where('study_program_id', $this->studyProgramFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.courses.index', [
            'rows' => $rows,
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
            'stats' => [
                'courses' => Course::count(),
                'topics' => Topic::count(),
                'materials' => Material::count(),
                'sessions' => VideoSession::count(),
                'assessments' => Assessment::count(),
                'certificates' => Certificate::count(),
            ],
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'study_program_id',
            'title',
            'slug',
            'poster',
            'description',
            'status',
        ]);

        $this->status = 'active';
    }
}