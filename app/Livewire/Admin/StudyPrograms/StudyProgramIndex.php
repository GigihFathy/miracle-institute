<?php

namespace App\Livewire\Admin\StudyPrograms;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\StudyProgram;
use Livewire\Component;
use Illuminate\Support\Str;

class StudyProgramIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;

    public ?string $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $status = 'active';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = StudyProgram::findOrFail($id);

        $this->editingId = $row->id;
        $this->title = $row->title;
        $this->description = $row->description;
        $this->status = $row->status;

        $this->showModal = true;
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    private function toast(string $type, string $message): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }

    public function save(): void
    {
        $this->validate();

        StudyProgram::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        $this->toast('success', 'Study program berhasil disimpan.');
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            $this->toast('warning', 'Pilih study program yang akan dihapus.');
            return;
        }

        StudyProgram::findOrFail($this->deleteId)->delete();

        $this->deleteId = null;
        $this->showDeleteModal = false;
        $this->toast('success', 'Study program berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.study-programs.index', [
            'rows' => StudyProgram::when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate($this->perPage),
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'status']);
        $this->status = 'active';
    }
}