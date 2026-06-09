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
    public string $certificate_course_number = '';
    public string $certificate_prefix_code = '';
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
            'certificate_course_number' => 'required|integer|min:1|max:999',
            'certificate_prefix_code' => 'required|string|max:50',
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
        $this->certificate_course_number = $row->certificate_course_number ? (string) $row->certificate_course_number : '';
        $this->certificate_prefix_code = $row->certificate_prefix_code ?? '';
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
                'certificate_course_number' => (int) $this->certificate_course_number,
                'certificate_prefix_code' => Str::upper(trim($this->certificate_prefix_code)),
                'description' => $this->description,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Course berhasil disimpan.');
    }

    public function getCertificateNumberPreviewProperty(): string
    {
        $courseNumber = str_pad(
            (string) ((int) $this->certificate_course_number > 0 ? (int) $this->certificate_course_number : 1),
            3,
            '0',
            STR_PAD_LEFT
        );

        $prefixCode = trim($this->certificate_prefix_code) !== ''
            ? Str::upper(trim($this->certificate_prefix_code))
            : $this->buildCertificatePrefixPreview();

        return sprintf(
            '%s-%s/%s/%s/%s',
            '00001',
            $courseNumber,
            $prefixCode,
            now()->format('m'),
            now()->format('Y')
        );
    }

    public function delete(string $id): void
    {
        Course::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Course berhasil dihapus.');
    }

    public function mount(): void
    {
        $this->showModal = false;
        $this->loadThumbnails();
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
            'certificate_course_number',
            'certificate_prefix_code',
            'description',
            'status',
        ]);

        $this->status = 'active';
        $this->resetValidation();
    }

    private function loadThumbnails(): void
    {
        $dir = public_path('images/thumbnail');

        if (!File::exists($dir)) {
            $this->thumbnails = [];
            return;
        }

        $files = collect(File::files($dir))
            ->filter(fn ($file) => in_array(Str::lower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        $this->thumbnails = $files
            ->map(fn ($file) => 'images/thumbnail/' . $file->getFilename())
            ->all();
    }

    private function buildCertificatePrefixPreview(): string
    {
        $source = trim($this->slug !== '' ? $this->slug : $this->title);

        if ($source === '') {
            return 'CRS';
        }

        $words = preg_split('/[\s\-_]+/', Str::upper($source)) ?: [];
        $code = '';

        foreach ($words as $word) {
            $word = preg_replace('/[^A-Z0-9]/', '', $word);

            if ($word !== '') {
                $code .= substr($word, 0, 1);
            }
        }

        return $code !== '' ? $code : 'CRS';
    }
}
