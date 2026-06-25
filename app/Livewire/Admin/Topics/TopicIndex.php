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
use App\Services\LearningAccessRequirementService;
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
    public string $status = 'draft';
    public int $sort_order = 0;

    public string $teacherFilter = '';
    public string $statusFilter = '';

    public ?Course $selectedCourse = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'teacherFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount(?string $courseFilter = null): void
    {
        $this->showModal = false;

        if ($courseFilter) {
            $this->selectedCourse = Course::find($courseFilter);
        }
    }

    protected function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name' => 'nullable|string|max:70',
            'description' => 'nullable|string',
            'status' => 'required|in:published,archived,draft',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function create(): void
    {
        if ($this->selectedCourse) {
            $this->course_id = $this->selectedCourse->id;
        }

        $this->resetForm();

        if ($this->selectedCourse) {
            $this->course_id = $this->selectedCourse->id;
        }

        $this->sort_order = $this->nextSortOrderForCourse($this->course_id);

        $this->showModal = true;
    }

    public function updatedCourseId(): void
    {
        if ($this->editingId) {
            return;
        }

        $this->sort_order = $this->nextSortOrderForCourse($this->course_id);
    }

    public function updatedName($value): void
    {
        $this->name = mb_substr((string) $value, 0, 70);
    }

    public function edit(string $id): void
    {
        $row = Topic::findOrFail($id);

        $this->editingId = $row->id;
        $this->course_id = $row->course_id;
        $this->teacher_id = $row->teacher_id ?? '';
        $this->name = $row->name;
        $this->description = $row->description;
        $this->status = $row->status === 'active' ? 'published' : $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $course = Course::findOrFail($this->course_id);
        $topic = $this->editingId ? Topic::findOrFail($this->editingId) : new Topic();

        $slug = filled($this->name)
            ? Str::slug($this->name)
            : ($this->editingId ? ($topic->slug ?: 'sesi-' . Str::random(8)) : 'sesi-' . Str::random(8));

        $data = [
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id !== '' ? $this->teacher_id : null,
            'name' => $this->name,
            'category' => Str::slug($course->title),
            'slug' => $slug,
            'description' => $this->description,
            'status' => $this->normalizeStatus($this->status),
            'sort_order' => $this->sort_order,
        ];

        $topic->forceFill($data);

        if ($this->normalizeStatus($this->status) === 'published') {
            try {
                app(LearningAccessRequirementService::class)->ensureTopicCanBePublished($topic);
            } catch (\RuntimeException $e) {
                $this->addError('status', $e->getMessage());

                return;
            }
        }

        Topic::updateOrCreate(['id' => $this->editingId], $data);

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
            ->when($this->selectedCourse, fn ($q) => $q->where('course_id', $this->selectedCourse->id))
            ->when($this->teacherFilter, fn ($q) => $q->where('teacher_id', $this->teacherFilter))
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'published') {
                    $q->whereIn('status', ['published', 'active']);
                    return;
                }

                $q->where('status', $this->statusFilter);
            })
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        $selectedCourse = $this->selectedCourse;

        return view('livewire.admin.topics.index', [
            'rows' => $rows,
            'teachers' => User::whereHas('roles', fn ($q) => $q->where('name', 'disciples'))
                ->orderBy('name')
                ->get(),
            'selectedCourse' => $selectedCourse,
            'stats' => [
                'courses' => 1,
                'topics' => $selectedCourse ? Topic::where('course_id', $selectedCourse->id)->count() : Topic::count(),
                'materials' => $selectedCourse
                    ? Material::whereHas('topic', fn ($q) => $q->where('course_id', $selectedCourse->id))->count()
                    : Material::count(),
                'sessions' => $selectedCourse
                    ? VideoSession::whereHas('topic', fn ($q) => $q->where('course_id', $selectedCourse->id))->count()
                    : VideoSession::count(),
                'certificates' => $selectedCourse
                    ? Certificate::where('course_id', $selectedCourse->id)->count()
                    : Certificate::count(),
            ],
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'course_id',
            'teacher_id',
            'name',
            'description',
            'status',
            'sort_order',
        ]);

        $this->status = 'draft';
        $this->sort_order = 1;
    }

    private function nextSortOrderForCourse(?string $courseId): int
    {
        if (!$courseId) {
            return 1;
        }

        $lastSortOrder = Topic::query()
            ->where('course_id', $courseId)
            ->max('sort_order');

        return max(1, ((int) $lastSortOrder) + 1);
    }

    private function normalizeStatus(string $status): string
    {
        return $status === 'active' ? 'published' : $status;
    }
}
