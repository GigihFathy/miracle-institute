<?php

namespace App\Livewire\Admin\Roles;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;

class RoleIndex extends Component
{
    use WithAdminTableState;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;

    public ?string $editingId = null;
    public string $name = '';
    public string $label = '';
    public ?string $description = null;
    public array $permissionIds = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissionIds' => 'array',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = Role::with('permissions')->findOrFail($id);

        $this->editingId = $row->id;
        $this->name = $row->name;
        $this->label = $row->label;
        $this->description = $row->description;
        $this->permissionIds = $row->permissions->pluck('id')->toArray();

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

        $role = Role::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'label' => $this->label,
                'description' => $this->description,
            ]
        );

        $role->permissions()->sync($this->permissionIds);

        $this->resetForm();
        $this->showModal = false;
        $this->toast('success', 'Role berhasil disimpan.');
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            $this->toast('warning', 'Pilih role yang akan dihapus.');
            return;
        }

        Role::findOrFail($this->deleteId)->delete();

        $this->deleteId = null;
        $this->showDeleteModal = false;
        $this->toast('success', 'Role berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.roles.index', [
            'rows' => Role::with('permissions')->latest()->paginate($this->perPage),
            'permissions' => Permission::all()->sortBy('name')->values(),
        ])->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'label', 'description', 'permissionIds']);
    }
}