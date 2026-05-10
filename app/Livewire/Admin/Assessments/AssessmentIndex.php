<?php

namespace App\Livewire\Admin\Assessments;

use App\Livewire\Concerns\WithAdminTableState;
use Illuminate\Validation\Rule;
use App\Models\Assessment;
use App\Models\Course;
use Livewire\Component;

class AssessmentIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;

    public ?string $editingId = null;

    public string $course_id = '';
    public string $title = '';
    public int $passing_grade = 70;
    public bool $randomize_questions = false;
    public ?int $question_limit = null;
    public string $status = 'active';

    public string $courseFilter = '';
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

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
            'status' => 'required|in:active,inactive,draft',
        ];
    }

    public function updatedCourseFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
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
        $this->status = $row->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->editingId) {
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
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Assessment berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        Assessment::findOrFail($id)->delete();
        session()->flash('success', 'Assessment berhasil dihapus.');
    }

    public function render()
    {
        $rows = Assessment::with('course')
            ->withCount(['questions', 'attempts'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.assessments.index', [
            'rows' => $rows,
            'courses' => Course::orderBy('title')->get(),
        ])->layout('layouts.admin');
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
            'status',
        ]);

        $this->passing_grade = 70;
        $this->status = 'active';
        $this->randomize_questions = false;
    }
}