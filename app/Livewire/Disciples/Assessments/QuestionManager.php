<?php

namespace App\Livewire\Disciples\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Str;
use Livewire\Component;

class QuestionManager extends Component
{
    public Assessment $assessment;

    public bool $openModal = false;
    public ?string $editingId = null;
    public bool $isSaving = false;
    public bool $dirty = false;

    public string $search = '';
    public string $question = '';
    public int $correctIndex = 0;
    public int $sort_order = 0;

    public array $options = [];

    public function mount(string $assessmentId): void
    {
        $this->assessment = Assessment::with(['course.studyProgram'])->findOrFail($assessmentId);
        $this->resetForm();
    }

    protected function defaultOptions(): array
    {
        return [
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
            ['id' => null, 'option_text' => ''],
        ];
    }

    protected function rules(): array
    {
        return [
            'question' => 'required|string|min:10',
            'options' => 'required|array|size:4',
            'options.*.option_text' => 'required|string|min:1',
            'correctIndex' => 'required|integer|min:0|max:3',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function updated($name): void
    {
        if (
            $this->openModal &&
            ! str_starts_with($name, 'search') &&
            in_array($name, ['question', 'sort_order', 'correctIndex'], true) ||
            str_starts_with($name, 'options.')
        ) {
            $this->dirty = true;
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->openModal = true;
        $this->dirty = false;
    }

    public function edit(string $id): void
    {
        $row = Question::with('options')->findOrFail($id);

        $this->editingId = $row->id;
        $this->question = $row->question;
        $this->sort_order = (int) $row->sort_order;

        $sorted = $row->options->sortBy('sort_order')->values();

        $this->options = $sorted->map(fn ($opt) => [
            'id' => $opt->id,
            'option_text' => $opt->option_text,
        ])->toArray();

        $this->correctIndex = max(0, (int) $sorted->search(fn ($opt) => $opt->is_correct));
        $this->openModal = true;
        $this->dirty = false;
    }

    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            $this->validate();

            $this->question = $this->sanitizeText($this->question);
            $this->options = array_map(function ($opt) {
                $opt['option_text'] = $this->sanitizeText($opt['option_text'] ?? '');
                return $opt;
            }, $this->options);

            $this->ensureValidCorrectAnswer();
            $this->ensureNoDuplicateOptions();

            $nextSort = Question::where('assessment_id', $this->assessment->id)->max('sort_order');
            $nextSort = $nextSort ? $nextSort + 1 : 1;

            $question = Question::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'assessment_id' => $this->assessment->id,
                    'question_type' => 'mcq',
                    'question' => $this->question,
                    'sort_order' => $this->editingId ? $this->sort_order : $nextSort,
                ]
            );

            QuestionOption::where('question_id', $question->id)->delete();

            foreach ($this->options as $i => $opt) {
                QuestionOption::create([
                    'id' => (string) Str::uuid(),
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'is_correct' => $i === $this->correctIndex,
                    'sort_order' => $i + 1,
                ]);
            }

            $this->resetForm();
            $this->openModal = false;
            $this->dirty = false;

            $this->dispatch('question-saved');
            session()->flash('success', 'Question berhasil disimpan.');
        } finally {
            $this->isSaving = false;
        }
    }

    public function delete(string $id): void
    {
        $question = Question::with('options')->findOrFail($id);
        $question->options()->delete();
        $question->delete();

        session()->flash('success', 'Question berhasil dihapus.');
    }

    public function getFilteredQuestionsProperty()
    {
        return Question::with('options')
            ->where('assessment_id', $this->assessment->id)
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('question', 'like', '%' . $this->search . '%')
                        ->orWhereHas('options', fn ($opt) => $opt->where('option_text', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->values();
    }

    private function ensureValidCorrectAnswer(): void
    {
        if (! isset($this->options[$this->correctIndex])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correctIndex' => 'Jawaban benar belum dipilih dengan valid.',
            ]);
        }

        $correct = trim((string) ($this->options[$this->correctIndex]['option_text'] ?? ''));

        if ($correct === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correctIndex' => 'Jawaban benar tidak boleh kosong.',
            ]);
        }
    }

    private function ensureNoDuplicateOptions(): void
    {
        $normalized = collect($this->options)
            ->pluck('option_text')
            ->map(fn ($value) => mb_strtolower(trim((string) $value)))
            ->filter();

        if ($normalized->count() !== $normalized->unique()->count()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'options' => 'Opsi jawaban tidak boleh duplikat.',
            ]);
        }
    }

    private function sanitizeText(string $value): string
    {
        $value = preg_replace('#<script\b[^>]*>(.*?)</script>#is', '', $value) ?? $value;
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'question', 'correctIndex', 'sort_order']);
        $this->options = $this->defaultOptions();
        $this->correctIndex = 0;
        $this->sort_order = 0;
        $this->dirty = false;
    }

    public function render()
    {
        $questions = $this->filteredQuestions;
        $attemptsQuery = AssessmentAttempt::where('assessment_id', $this->assessment->id);

        return view('livewire.disciples.assessments.question-manager', [
            'questions' => $questions,
            'questionsCount' => $questions->count(),
            'attemptsCount' => (clone $attemptsQuery)->count(),
            'submittedAttemptsCount' => (clone $attemptsQuery)->whereNotNull('submitted_at')->count(),
            'passedAttemptsCount' => (clone $attemptsQuery)->where('passed', true)->count(),
        ])->layout('layouts.learning');
    }
}