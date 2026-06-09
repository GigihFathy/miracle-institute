<?php

namespace App\Livewire\Admin\Permissions;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Component;

class PermissionIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;

    public ?string $editingId = null;
    public string $name = '';

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('manage_users'), 403);

        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

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
        abort_unless(auth()->check() && auth()->user()->can('manage_users'), 403);

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($this->editingId, 'id'),
            ],
        ]);

        $name = trim($validated['name']);

        Permission::updateOrCreate(
            ['id' => $this->editingId],
            [
                'id' => $this->editingId ?? (string) Str::uuid(),
                'name' => $name,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
        $this->toast('success', 'Permission berhasil disimpan.');
    }

    public function delete(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('manage_users'), 403);

        if (!$this->deleteId) {
            $this->toast('warning', 'Pilih permission yang akan dihapus.');
            return;
        }

        $permission = Permission::query()
            ->withCount('roles')
            ->findOrFail($this->deleteId);

        if ($permission->roles_count > 0) {
            $this->toast('warning', 'Permission masih dipakai oleh role dan tidak bisa dihapus.');
            $this->deleteId = null;
            $this->showDeleteModal = false;
            return;
        }

        $permission->delete();

        $this->deleteId = null;
        $this->showDeleteModal = false;
        $this->toast('success', 'Permission berhasil dihapus.');
    }

    public function render()
    {
        $query = Permission::query();

        if ($this->search !== '') {
            $query->where(function ($builder) {
                $builder->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
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
