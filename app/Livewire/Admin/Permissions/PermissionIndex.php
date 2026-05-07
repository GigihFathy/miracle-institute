<?php

namespace App\Livewire\Admin\Permissions;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Permission;
use Livewire\Component;
use Illuminate\Support\Str;

class PermissionIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;

    public ?string $editingId = null;
    public string $name = '';

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Permission::findOrFail($id);
        $this->editingId = $row->id;
        $this->name = $row->name;
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
        $this->validate(['name' => 'required|string|max:255']);

        Permission::updateOrCreate(
            ['id' => $this->editingId],
            [
                'id' => $this->editingId ?? (string) Str::uuid(),
                'name' => $this->name,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        $this->toast('success', 'Permission berhasil disimpan.');
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            $this->toast('warning', 'Pilih permission yang akan dihapus.');
            return;
        }

        Permission::findOrFail($this->deleteId)->delete();

        $this->deleteId = null;
        $this->showDeleteModal = false;
        $this->toast('success', 'Permission berhasil dihapus.');
    }

    public function render()
    {
        $query = Permission::query();

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return view('livewire.admin.permissions.index', [
            'rows' => $query->latest()->paginate($this->perPage),
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name']);
    }
}