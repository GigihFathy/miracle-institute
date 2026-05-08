<?php

namespace App\Livewire\Disciples\Topics;

use App\Livewire\Concerns\WithTableState;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\User;
use App\Models\VideoSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class TopicIndex extends Component
{
    use WithTableState;

    public bool $showModal = false;

    public ?string $editingId = null;
    public ?string $selectedTopicId = null;

    public string $course_id = '';
    public string $teacher_id = '';
    public string $name = '';
    public string $category = '';
    public string $description = '';
    public string $poster = '';
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
        'perPage' => ['except' => 12],
    ];

    public function mount(): void
    {
        $this->syncSelectedTopicToFirstAvailable();
    }

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

    public function updatedTeacherFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedTopicToFirstAvailable();
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
        $this->category = $row->category ?? '';
        $this->description = $row->description;
        $this->poster = $row->poster ?? '';
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = (int) ($row->sort_order ?? 0);

        $this->showModal = true;
    }

    public function selectTopic(string $id): void
    {
        $this->selectedTopicId = $id;
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
                'category' => $this->category ?: ($course->studyProgram?->title ?? null),
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'poster' => $this->poster ?: null,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Topic berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Topic::findOrFail($id)->delete();

        if ($this->selectedTopicId === $id) {
            $this->syncSelectedTopicToFirstAvailable();
        }

        session()->flash('success', 'Topic berhasil dihapus.');
    }

    public function render()
    {
        $baseQuery = $this->baseTopicQuery();

        $topicIds = (clone $baseQuery)->pluck('id')->all();
        $topicCourseMap = (clone $baseQuery)->pluck('course_id', 'id')->all();
        $courseIds = array_values(array_unique(array_values($topicCourseMap)));

        $enrollmentCountsByCourse = [];
        if (! empty($courseIds)) {
            $enrollmentCountsByCourse = CourseEnrollment::query()
                ->whereIn('course_id', $courseIds)
                ->select('course_id', DB::raw('COUNT(*) as total'))
                ->groupBy('course_id')
                ->pluck('total', 'course_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        $completedCountsByTopic = [];
        if (! empty($topicIds)) {
            $completedCountsByTopic = TopicProgress::query()
                ->whereIn('topic_id', $topicIds)
                ->where('status', 'completed')
                ->select('topic_id', DB::raw('COUNT(*) as total'))
                ->groupBy('topic_id')
                ->pluck('total', 'topic_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        $durationColumn = $this->materialDurationColumn();

        $materialDurationByTopic = [];
        if ($durationColumn && ! empty($topicIds)) {
            $materialDurationByTopic = Material::query()
                ->whereIn('topic_id', $topicIds)
                ->select('topic_id', DB::raw("SUM({$durationColumn}) as total"))
                ->groupBy('topic_id')
                ->pluck('total', 'topic_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        $rows = (clone $baseQuery)
            ->with(['course.assessment', 'teacher'])
            ->withCount(['materials', 'videoSessions', 'certificates'])
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function (Topic $topic) use (
            $topicCourseMap,
            $completedCountsByTopic,
            $enrollmentCountsByCourse,
            $materialDurationByTopic,
            $durationColumn
        ) {
            $courseId = $topicCourseMap[$topic->id] ?? $topic->course_id;
            $enrollments = (int) ($enrollmentCountsByCourse[$courseId] ?? 0);
            $completed = (int) ($completedCountsByTopic[$topic->id] ?? 0);

            $completionRate = $enrollments > 0
                ? (int) round(($completed / $enrollments) * 100)
                : 0;

            $assessment = $topic->course?->assessment;

            $topic->setAttribute('completion_rate', $completionRate);
            $topic->setAttribute('material_duration_minutes', $materialDurationByTopic[$topic->id] ?? null);
            $topic->setAttribute('material_duration_text', $durationColumn
                ? $this->formatMinutes($materialDurationByTopic[$topic->id] ?? null)
                : 'N/A');
            $topic->setAttribute('assessment_label', $assessment
                ? ucfirst($assessment->status)
                : 'No assessment');
            $topic->setAttribute('assessment_badge_class', $this->assessmentBadgeClass($assessment?->status));

            return $topic;
        });

        $selectedTopic = $this->selectedTopicId
            ? Topic::with(['course.assessment', 'teacher'])
                ->withCount(['materials', 'videoSessions', 'certificates'])
                ->find($this->selectedTopicId)
            : null;

        if ($selectedTopic) {
            $courseId = $selectedTopic->course_id;
            $enrollments = (int) ($enrollmentCountsByCourse[$courseId] ?? 0);
            $completed = (int) ($completedCountsByTopic[$selectedTopic->id] ?? 0);

            $completionRate = $enrollments > 0
                ? (int) round(($completed / $enrollments) * 100)
                : 0;

            $assessment = $selectedTopic->course?->assessment;

            $selectedTopic->setAttribute('completion_rate', $completionRate);
            $selectedTopic->setAttribute('material_duration_minutes', $materialDurationByTopic[$selectedTopic->id] ?? null);
            $selectedTopic->setAttribute('material_duration_text', $durationColumn
                ? $this->formatMinutes($materialDurationByTopic[$selectedTopic->id] ?? null)
                : 'N/A');
            $selectedTopic->setAttribute('assessment_label', $assessment
                ? ucfirst($assessment->status)
                : 'No assessment');
            $selectedTopic->setAttribute('assessment_badge_class', $this->assessmentBadgeClass($assessment?->status));
        }

        $topicsInScope = count($topicIds);

        $completionRates = [];
        foreach ($topicIds as $topicId) {
            $courseId = $topicCourseMap[$topicId] ?? null;
            $enrollments = $courseId ? (int) ($enrollmentCountsByCourse[$courseId] ?? 0) : 0;
            $completed = (int) ($completedCountsByTopic[$topicId] ?? 0);

            $completionRates[] = $enrollments > 0
                ? round(($completed / $enrollments) * 100, 1)
                : 0;
        }

        $averageCompletionRate = $topicsInScope > 0
            ? round(array_sum($completionRates) / count($completionRates), 1)
            : 0;

        $materialsTotal = ! empty($topicIds)
            ? Material::whereIn('topic_id', $topicIds)->count()
            : 0;

        $materialDurationTotal = ($durationColumn && ! empty($topicIds))
            ? (int) Material::whereIn('topic_id', $topicIds)->sum($durationColumn)
            : null;

        $assessmentReadyCount = $topicsInScope > 0
            ? Topic::whereIn('id', $topicIds)
                ->whereHas('course.assessment', fn ($q) => $q->where('status', 'active'))
                ->count()
            : 0;

        $sessionsTotal = ! empty($topicIds)
            ? VideoSession::whereIn('topic_id', $topicIds)->count()
            : 0;

        $certificatesTotal = ! empty($topicIds)
            ? Certificate::whereIn('topic_id', $topicIds)->count()
            : 0;

        $statsCards = [
            [
                'label' => 'Topics in Scope',
                'value' => number_format($topicsInScope),
                'note' => 'Topic yang sesuai dengan filter aktif.',
            ],
            [
                'label' => 'Materials Total',
                'value' => number_format($materialsTotal),
                'note' => 'Seluruh material dalam scope topic.',
            ],
            [
                'label' => 'Cumulative Material Duration',
                'value' => $durationColumn ? $this->formatMinutes($materialDurationTotal) : 'N/A',
                'note' => $durationColumn
                    ? 'Akumulasi durasi material yang tersedia.'
                    : 'Tambahkan kolom duration_minutes pada materials jika ingin metrik durasi penuh.',
            ],
            [
                'label' => 'Average Topic Completion',
                'value' => $topicsInScope > 0 ? $averageCompletionRate . '%' : '0%',
                'note' => 'Rata-rata penyelesaian mahasiswa per topic.',
            ],
            [
                'label' => 'Assessment Ready',
                'value' => $topicsInScope > 0 ? round(($assessmentReadyCount / $topicsInScope) * 100, 1) . '%' : '0%',
                'note' => $assessmentReadyCount . ' topic memiliki assessment aktif.',
            ],
            [
                'label' => 'Sessions Total',
                'value' => number_format($sessionsTotal),
                'note' => 'Sesi aktif yang terhubung ke topic.',
            ],
        ];

        return view('livewire.disciples.topics.index', [
            'rows' => $rows,
            'selectedTopic' => $selectedTopic,
            'courses' => Course::orderBy('title')->get(),
            'teachers' => User::whereHas('roles', fn ($q) => $q->where('name', 'disciples'))
                ->orderBy('name')
                ->get(),
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
            ->when($this->teacherFilter, fn ($q) => $q->where('teacher_id', $this->teacherFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));
    }

    private function syncSelectedTopicToFirstAvailable(): void
    {
        $this->selectedTopicId = $this->baseTopicQuery()
            ->orderBy('course_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->value('id');
    }

    private function materialDurationColumn(): ?string
    {
        if (Schema::hasColumn('materials', 'duration_minutes')) {
            return 'duration_minutes';
        }

        if (Schema::hasColumn('materials', 'estimated_minutes')) {
            return 'estimated_minutes';
        }

        return null;
    }

    private function formatMinutes(?int $minutes): string
    {
        if ($minutes === null) {
            return 'N/A';
        }

        $minutes = max(0, $minutes);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . 'h ' . $remainingMinutes . 'm';
        }

        if ($hours > 0) {
            return $hours . 'h';
        }

        return $remainingMinutes . 'm';
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
            'course_id',
            'teacher_id',
            'name',
            'category',
            'description',
            'poster',
            'visibility',
            'status',
            'sort_order',
        ]);

        $this->visibility = 'Public';
        $this->status = 'active';
        $this->sort_order = 0;
    }
}