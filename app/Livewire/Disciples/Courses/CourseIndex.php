<?php

namespace App\Livewire\Disciples\Courses;

use App\Livewire\Concerns\WithTableState;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\StudyProgram;
use App\Models\Topic;
use App\Models\VideoSession;
use Illuminate\Support\Str;
use Livewire\Component;

class CourseIndex extends Component
{
    use WithTableState;

    public bool $showModal = false;

    public ?string $editingId = null;
    public ?string $selectedCourseId = null;

    public string $study_program_id = '';
    public string $title = '';
    public string $slug = '';
    public string $poster = '';
    public int $credit = 0;
    public int $quota = 0;
    public string $description = '';
    public string $status = 'active';

    public string $studyProgramFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'studyProgramFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 12],
    ];

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
            'status' => 'required|in:active,inactive',
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
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Course::findOrFail($id);

        $this->editingId = $row->id;
        $this->study_program_id = $row->study_program_id;
        $this->title = $row->title;
        $this->slug = $row->slug;
        $this->poster = $row->poster ?? '';
        $this->credit = (int) $row->credit;
        $this->quota = (int) $row->quota;
        $this->description = $row->description;
        $this->status = $row->status;

        $this->showModal = true;
    }

    public function selectCourse(string $id): void
    {
        $this->selectedCourseId = $id;
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
                'poster' => $this->poster ?: null,
                'credit' => $this->credit,
                'quota' => $this->quota,
                'description' => $this->description,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Course berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Course::findOrFail($id)->delete();
        session()->flash('success', 'Course berhasil dihapus.');
    }

    public function render()
    {
        $rows = Course::with([
                'studyProgram',
                'assessment',
                'topics' => fn ($q) => $q->withCount(['materials'])->orderBy('sort_order')->orderBy('name'),
            ])
            ->withCount(['topics', 'enrollments', 'certificates'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->studyProgramFilter, fn ($q) => $q->where('study_program_id', $this->studyProgramFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function (Course $course) {
            $enrollments = (int) ($course->enrollments_count ?? 0);
            $certificates = (int) ($course->certificates_count ?? 0);

            $course->setAttribute('completion_rate', $enrollments > 0 ? (int) round(($certificates / $enrollments) * 100) : 0);
            $course->setAttribute('assessment_state', $course->assessment?->status ?? null);
            $course->setAttribute('assessment_label', $course->assessment
                ? (strtolower($course->assessment->status) === 'active' ? 'Assessment active' : ucfirst($course->assessment->status))
                : 'No assessment');

            $course->setAttribute('assessment_available', (bool) $course->assessment);

            return $course;
        });

        $selectedCourse = $this->selectedCourseId
            ? Course::with(['studyProgram', 'assessment', 'topics.materials', 'topics.videoSessions'])
                ->withCount(['topics', 'enrollments', 'certificates'])
                ->find($this->selectedCourseId)
            : null;

        if ($selectedCourse) {
            $enrollments = (int) ($selectedCourse->enrollments_count ?? 0);
            $certificates = (int) ($selectedCourse->certificates_count ?? 0);

            $selectedCourse->setAttribute('completion_rate', $enrollments > 0 ? (int) round(($certificates / $enrollments) * 100) : 0);
            $selectedCourse->setAttribute('assessment_available', (bool) $selectedCourse->assessment);
            $selectedCourse->setAttribute('assessment_label', $selectedCourse->assessment
                ? (strtolower($selectedCourse->assessment->status) === 'active' ? 'Assessment active' : ucfirst($selectedCourse->assessment->status))
                : 'No assessment');
        }

        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'active')->count();
        $coursesWithAssessment = Course::whereHas('assessment')->count();
        $totalTopics = Topic::count();
        $totalEnrollments = CourseEnrollment::count();
        $totalCertificates = Certificate::count();
        $totalSessions = VideoSession::count();

        $statsCards = [
            [
                'label' => 'Active course coverage',
                'value' => $totalCourses > 0 ? round(($activeCourses / $totalCourses) * 100, 1) . '%' : '0%',
                'note' => $activeCourses . ' active from ' . $totalCourses . ' courses',
            ],
            [
                'label' => 'Assessment coverage',
                'value' => $totalCourses > 0 ? round(($coursesWithAssessment / $totalCourses) * 100, 1) . '%' : '0%',
                'note' => $coursesWithAssessment . ' courses already have assessment',
            ],
            [
                'label' => 'Certificate yield',
                'value' => $totalEnrollments > 0 ? round(($totalCertificates / $totalEnrollments) * 100, 1) . '%' : '0%',
                'note' => $totalCertificates . ' certificates from ' . $totalEnrollments . ' enrollments',
            ],
            [
                'label' => 'Topics per course',
                'value' => $totalCourses > 0 ? number_format($totalTopics / $totalCourses, 1) : '0.0',
                'note' => $totalTopics . ' topics in total',
            ],
            [
                'label' => 'Sessions per topic',
                'value' => $totalTopics > 0 ? number_format($totalSessions / $totalTopics, 1) : '0.0',
                'note' => $totalSessions . ' sessions across all topics',
            ],
            [
                'label' => 'Enrollments per course',
                'value' => $totalCourses > 0 ? number_format($totalEnrollments / $totalCourses, 1) : '0.0',
                'note' => $totalEnrollments . ' enrollments in total',
            ],
        ];

        return view('livewire.disciples.courses.index', [
            'rows' => $rows,
            'selectedCourse' => $selectedCourse,
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
            'statsCards' => $statsCards,
        ])->layout('layouts.learning');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'study_program_id',
            'title',
            'slug',
            'poster',
            'credit',
            'quota',
            'description',
            'status',
        ]);

        $this->credit = 0;
        $this->quota = 0;
        $this->status = 'active';
    }
}