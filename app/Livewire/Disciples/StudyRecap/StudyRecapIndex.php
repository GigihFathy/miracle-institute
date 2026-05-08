<?php

namespace App\Livewire\Disciples\StudyRecap;

use App\Livewire\Concerns\WithTableState;
use App\Livewire\Concerns\ResolvesStudyRecapMetrics;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Support\Collection;
use Livewire\Component;

class StudyRecapIndex extends Component
{
    use WithTableState;
    use ResolvesStudyRecapMetrics;

    public ?string $selectedCourseId = null;
    public bool $showModal = false;

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
        if (session('active_role') !== 'disciples') {
            abort(403);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function selectCourse(string $courseId): void
    {
        $this->selectedCourseId = $courseId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function openSelectedCourse(): void
    {
        if ($this->selectedCourseId) {
            $this->showModal = true;
        }
    }

    public function render()
    {
        $courseIds = $this->mentorCourseIds();

        $courseQuery = Course::query()
            ->with(['studyProgram', 'assessment', 'topics.materials', 'topics.videoSessions', 'enrollments.user'])
            ->withCount(['topics', 'enrollments', 'certificates'])
            ->whereIn('id', $courseIds)
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('studyProgram', fn ($sp) => $sp->where('title', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('topics', fn ($t) => $t->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('id', $this->courseFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest();

        $filteredCourseIds = (clone $courseQuery)->pluck('id')->values();

        $rows = $courseQuery->paginate($this->perPage);
        $rows->getCollection()->transform(fn (Course $course) => $this->decorateCourseRow($course));

        if ($filteredCourseIds->isEmpty()) {
            $this->selectedCourseId = null;
            $this->showModal = false;
        } elseif (! $this->selectedCourseId || ! $filteredCourseIds->contains($this->selectedCourseId)) {
            $this->selectedCourseId = (string) $rows->getCollection()->first()?->id;
        }

        $selectedCourse = $this->selectedCourseId
            ? Course::query()
                ->with(['studyProgram', 'assessment', 'topics.materials', 'topics.videoSessions', 'enrollments.user'])
                ->withCount(['topics', 'enrollments', 'certificates'])
                ->whereIn('id', $courseIds)
                ->find($this->selectedCourseId)
            : null;

        $selectedCourse = $selectedCourse ? $this->decorateCourseRow($selectedCourse) : null;
        $selectedCourseSummary = $selectedCourse ? $this->buildCourseSummary($selectedCourse) : null;

        $overall = $this->buildOverallSummary($courseIds);

        $courseOptions = Course::query()
            ->whereIn('id', $courseIds)
            ->with('studyProgram')
            ->orderBy('title')
            ->get();

        return view('livewire.disciples.study-recap.index', [
            'rows' => $rows,
            'selectedCourse' => $selectedCourse,
            'selectedCourseSummary' => $selectedCourseSummary,
            'overall' => $overall,
            'courseOptions' => $courseOptions,
        ])->layout('layouts.learning');
    }
}