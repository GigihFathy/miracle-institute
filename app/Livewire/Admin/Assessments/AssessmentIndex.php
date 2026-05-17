<?php

namespace App\Livewire\Admin\Assessments;

use App\Livewire\Concerns\WithAdminTableState;
use Illuminate\Validation\Rule;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Str;
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

    // Question manager state
    public ?Assessment $questionAssessment = null;
    public bool $questionModalOpen = false;

    public ?string $questionEditingId = null;
    public string $question_text = '';
    public int $question_correctIndex = 0;
    public int $question_sort_order = 0;
    public array $question_options = [];

    public function mount(?string $courseFilter = null): void
    {
        $this->courseFilter = $courseFilter ?? '';
    }

    protected $queryString = [
        'search' => ['except' => ''],
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

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    protected function currentAssessment(): ?Assessment
    {
        if (!$this->courseFilter) {
            return null;
        }

        return Assessment::with(['course', 'questions.options'])
            ->withCount(['attempts'])
            ->where('course_id', $this->courseFilter)
            ->first();
    }

    public function create(): void
    {
        // Prevent creating a second assessment for a scoped course
        if ($this->courseFilter && $this->currentAssessment()) {
            session()->flash('error', 'Course sudah memiliki assessment.');
            return;
        }

        $this->resetForm();
        $this->showModal = true;
    }

    // --- Question manager methods ---
    protected function questionDefaultOptions(): array
    {
        return [
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
        ];
    }

    protected function questionRules(): array
    {
        return [
            'question_text' => 'required|string|min:10',
            'question_options' => 'required|array|size:4',
            'question_options.*.option_text' => 'required|string|min:1',
            'question_correctIndex' => 'required|integer|min:0|max:3',
            'question_sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function openQuestionManager(string $assessmentId): void
    {
        $assessment = $this->currentAssessment();

        if (!$assessment) {
            session()->flash('error', 'Assessment tidak ditemukan.');
            return;
        }

        $this->questionAssessment = $assessment;

        $this->resetQuestionForm();
        $this->questionModalOpen = true;
    }

    public function createQuestion(): void
    {
        $this->resetQuestionForm();
        $this->questionModalOpen = true;
    }

    public function editQuestion(string $id): void
    {
        $row = Question::with('options')->findOrFail($id);

        $this->questionEditingId = $row->id;
        $this->question_text = $row->question;
        $this->question_sort_order = (int) $row->sort_order;

        $sorted = $row->options->sortBy('sort_order')->values();

        $this->question_options = $sorted->map(fn ($opt) => [
            'id' => $opt->id,
            'option_text' => $opt->option_text,
        ])->toArray();

        $this->question_correctIndex = max(0, (int) $sorted->search(fn ($opt) => $opt->is_correct));
        $this->questionModalOpen = true;
    }

    public function saveQuestion(): void
    {
        $this->validate($this->questionRules());

        $assessment = $this->questionAssessment ?? $this->currentAssessment();

        if (!$assessment) {
            session()->flash('error', 'Assessment tidak ditemukan.');
            return;
        }

        $this->questionAssessment = $assessment;

        $nextSort = Question::where('assessment_id', $assessment->id)->max('sort_order');
        $nextSort = $nextSort ? $nextSort + 1 : 1;

        $question = Question::updateOrCreate(
            ['id' => $this->questionEditingId],
            [
                'assessment_id' => $assessment->id,
                'question_type' => 'mcq',
                'question' => $this->question_text,
                'sort_order' => $this->questionEditingId ? $this->question_sort_order : $nextSort,
            ]
        );

        QuestionOption::where('question_id', $question->id)->delete();

        foreach ($this->question_options as $i => $opt) {
            QuestionOption::create([
                'id' => (string) Str::uuid(),
                'question_id' => $question->id,
                'option_text' => $opt['option_text'],
                'is_correct' => $i === $this->question_correctIndex,
                'sort_order' => $i + 1,
            ]);
        }

        $this->resetQuestionForm();
        $this->questionModalOpen = false;
        session()->flash('success', 'Question berhasil disimpan.');
    }

    public function deleteQuestion(string $id): void
    {
        Question::findOrFail($id)->delete();
        session()->flash('success', 'Question berhasil dihapus.');
    }

    private function resetQuestionForm(): void
    {
        $this->reset(['questionEditingId', 'question_text', 'question_correctIndex', 'question_sort_order']);
        $this->question_options = $this->questionDefaultOptions();
        $this->question_correctIndex = 0;
        $this->question_sort_order = 0;
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
        $selectedAssessment = $this->currentAssessment();

        if ($selectedAssessment) {
            $this->questionAssessment = $selectedAssessment;
        }

        return view('livewire.admin.assessments.index', [
            'courses' => Course::orderBy('title')->get(),
            'selectedCourse' => $this->courseFilter ? Course::find($this->courseFilter) : null,
            'selectedAssessment' => $selectedAssessment,
            'selectedCourseHasAssessment' => (bool) $selectedAssessment,
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