<?php

namespace App\Livewire\Admin\Courses;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\VideoSession;
use App\Services\LearningAccessRequirementService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class CourseIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public bool $showRecapModal = false;

    /** @var array<string> */
    public array $thumbnails = [];

    public ?string $editingId = null;
    public string $title = '';
    public string $slug = '';
    public string $poster = '';
    public string $certificate_course_number = '';
    public string $certificate_prefix_code = '';
    public string $description = '';
    public string $status = 'inactive';

    public string $statusFilter = '';
    public ?Course $selectedCourseRecap = null;
    public array $courseRecapSummary = [];
    public array $courseRecapSessions = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
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
        $this->title = mb_substr((string) $value, 0, 150);

        if (!$this->editingId) {
            $this->slug = Str::slug($this->title);
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

        $course = $this->editingId ? Course::findOrFail($this->editingId) : new Course();

        $course->forceFill([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'poster' => $this->poster,
            'certificate_course_number' => (int) $this->certificate_course_number,
            'certificate_prefix_code' => Str::upper(trim($this->certificate_prefix_code)),
            'description' => $this->description,
            'status' => $this->status,
        ]);

        if ($this->status === 'active') {
            try {
                app(LearningAccessRequirementService::class)->ensureCourseCanBeActivated($course);
            } catch (\RuntimeException $e) {
                $this->addError('status', $e->getMessage());

                return;
            }
        }

        Course::updateOrCreate(
            ['id' => $this->editingId],
            [
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

    public function openRecap(string $id): void
    {
        $course = Course::with([
            'topics.videoSessions',
            'enrollments',
            'certificates' => fn ($query) => $query->where('status', 'issued'),
        ])->findOrFail($id);

        $sessionIds = $course->topics
            ->flatMap(fn ($topic) => $topic->videoSessions->pluck('id'))
            ->values();

        $attendances = Attendance::query()
            ->whereIn('video_session_id', $sessionIds)
            ->get()
            ->groupBy('video_session_id');

        $sessions = $course->topics
            ->flatMap(function ($topic) use ($attendances) {
                return $topic->videoSessions->map(function ($session) use ($topic, $attendances) {
                    $rows = collect($attendances->get($session->id, collect()));

                    return [
                        'topic_name' => $topic->name,
                        'session_title' => $session->title,
                        'start_at' => $session->start_at?->format('d M Y H:i') ?? '-',
                        'status' => $session->status,
                        'present' => $rows->where('status', 'present')->count(),
                        'late' => $rows->where('status', 'late')->count(),
                        'absent' => $rows->whereIn('status', ['online', 'absent'])->count(),
                        'attendance_total' => $rows->count(),
                    ];
                });
            })
            ->sortBy('start_at')
            ->values();

        $this->selectedCourseRecap = $course;
        $this->courseRecapSessions = $sessions->all();
        $this->courseRecapSummary = [
            'enrollments_total' => $course->enrollments->count(),
            'graduates_total' => $course->certificates->count(),
            'sessions_total' => $sessions->count(),
            'attendance_present' => $sessions->sum('present'),
            'attendance_late' => $sessions->sum('late'),
            'attendance_absent' => $sessions->sum('absent'),
        ];
        $this->showRecapModal = true;
    }

    public function closeRecapModal(): void
    {
        $this->showRecapModal = false;
        $this->selectedCourseRecap = null;
        $this->courseRecapSummary = [];
        $this->courseRecapSessions = [];
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
        $rows = Course::withCount(['topics', 'enrollments', 'certificates'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.courses.index', [
            'rows' => $rows,
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
            'title',
            'slug',
            'poster',
            'certificate_course_number',
            'certificate_prefix_code',
            'description',
            'status',
        ]);

        $this->status = 'inactive';
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
