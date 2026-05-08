<?php

namespace App\Livewire\Disciples\Assessments;

use App\Livewire\Concerns\WithTableState;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AssessmentIndex extends Component
{
    use WithTableState;

    public bool $showModal = false;
    public ?string $editingId = null;
    public ?string $selectedAssessmentId = null;
    public bool $isSaving = false;

    public string $course_id = '';
    public string $title = '';
    public int $passing_grade = 70;
    public bool $randomize_questions = false;
    public ?int $question_limit = null;
    public ?int $time_limit_minutes = null;
    public string $status = 'active';

    public string $courseFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 12],
    ];

    public function mount(): void
    {
        $this->syncSelectedAssessmentToFirstAvailable();
    }

    protected function rules(): array
    {
        return [
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('assessments', 'course_id')->ignore($this->editingId),
            ],
            'title' => 'required|string|max:255',
            'passing_grade' => 'required|integer|min:0|max:100',
            'randomize_questions' => 'boolean',
            'question_limit' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,draft',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->syncSelectedAssessmentToFirstAvailable();
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAssessmentToFirstAvailable();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->syncSelectedAssessmentToFirstAvailable();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Assessment::findOrFail($id);

        $this->editingId = $row->id;
        $this->course_id = $row->course_id;
        $this->title = $row->title;
        $this->passing_grade = (int) $row->passing_grade;
        $this->randomize_questions = (bool) $row->randomize_questions;
        $this->question_limit = $row->question_limit;
        $this->time_limit_minutes = $row->time_limit_minutes;
        $this->status = $row->status;

        $this->selectedAssessmentId = $row->id;
        $this->showModal = true;
    }

    public function selectAssessment(string $id): void
    {
        $this->selectedAssessmentId = $id;
    }

    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            $this->validate();

            if (! $this->editingId) {
                $exists = Assessment::where('course_id', $this->course_id)->exists();

                if ($exists) {
                    $this->addError('course_id', 'Course sudah memiliki assessment.');
                    return;
                }
            }

            Assessment::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'course_id' => $this->course_id,
                    'title' => $this->title,
                    'passing_grade' => $this->passing_grade,
                    'randomize_questions' => $this->randomize_questions,
                    'question_limit' => $this->question_limit,
                    'time_limit_minutes' => $this->time_limit_minutes,
                    'status' => $this->status,
                ]
            );

            $this->resetForm();
            $this->showModal = false;
            $this->syncSelectedAssessmentToFirstAvailable();

            session()->flash('success', 'Assessment berhasil disimpan.');
        } finally {
            $this->isSaving = false;
        }
    }

    public function delete(string $id): void
    {
        Assessment::findOrFail($id)->delete();

        if ($this->selectedAssessmentId === $id) {
            $this->syncSelectedAssessmentToFirstAvailable();
        }

        session()->flash('success', 'Assessment berhasil dihapus.');
    }

    public function render()
    {
        $baseQuery = Assessment::with('course.studyProgram')
            ->withCount(['questions', 'attempts'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $rows = (clone $baseQuery)
            ->latest()
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function (Assessment $assessment) {
            $assessment->setAttribute('time_label', $assessment->time_limit_minutes ? $assessment->time_limit_minutes . ' min' : 'No limit');
            $assessment->setAttribute('question_mix_label', $assessment->questions_count . ' questions');
            $assessment->setAttribute('status_badge_class', $this->statusBadgeClass($assessment->status));

            return $assessment;
        });

        $selectedAssessment = $this->selectedAssessmentId
            ? Assessment::with(['course.studyProgram'])->withCount(['questions', 'attempts'])->find($this->selectedAssessmentId)
            : null;

        if ($selectedAssessment) {
            $selectedAssessment->setAttribute(
                'time_label',
                $selectedAssessment->time_limit_minutes ? $selectedAssessment->time_limit_minutes . ' min' : 'No limit'
            );
            $selectedAssessment->setAttribute('status_badge_class', $this->statusBadgeClass($selectedAssessment->status));
            $selectedAssessment->setAttribute('question_type_summary', $this->questionTypeSummary($selectedAssessment->id));
        }

        $statsQuery = clone $baseQuery;

        $statsCards = [
            [
                'label' => 'Total Assessments',
                'value' => number_format((clone $statsQuery)->count()),
                'note' => 'Semua assessment dalam scope filter aktif.',
            ],
            [
                'label' => 'Active',
                'value' => number_format((clone $statsQuery)->where('status', 'active')->count()),
                'note' => 'Assessment yang saat ini aktif.',
            ],
            [
                'label' => 'Timed',
                'value' => number_format((clone $statsQuery)->whereNotNull('time_limit_minutes')->count()),
                'note' => 'Assessment dengan batas waktu pengerjaan.',
            ],
            [
                'label' => 'Avg Passing',
                'value' => (int) round((clone $statsQuery)->avg('passing_grade') ?: 0) . '%',
                'note' => 'Rata-rata ambang kelulusan.',
            ],
            [
                'label' => 'Total Questions',
                'value' => number_format((clone $statsQuery)->get()->sum('questions_count')),
                'note' => 'Akumulasi soal dari hasil count relation.',
            ],
            [
                'label' => 'MCQ Items',
                'value' => number_format(Question::whereHas('assessment', function ($q) use ($statsQuery) {
                    $q->whereIn('course_id', (clone $statsQuery)->pluck('course_id')->filter()->values());
                })->count()),
                'note' => 'Kategori soal saat ini berbasis MCQ.',
            ],
        ];

        return view('livewire.disciples.assessments.index', [
            'rows' => $rows,
            'selectedAssessment' => $selectedAssessment,
            'courses' => Course::with('studyProgram')->orderBy('title')->get(),
            'statsCards' => $statsCards,
        ])->layout('layouts.learning');
    }

    private function questionTypeSummary(string $assessmentId): string
    {
        $mcqCount = Question::where('assessment_id', $assessmentId)->count();

        return 'MCQ: ' . number_format($mcqCount);
    }

    private function statusBadgeClass(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
            'inactive' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-500 border-slate-200',
        };
    }

    private function syncSelectedAssessmentToFirstAvailable(): void
    {
        $this->selectedAssessmentId = Assessment::query()
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->value('id');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'course_id',
            'title',
            'passing_grade',
            'randomize_questions',
            'question_limit',
            'time_limit_minutes',
            'status',
        ]);

        $this->passing_grade = 70;
        $this->status = 'active';
        $this->randomize_questions = false;
    }
}