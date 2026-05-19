<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

trait WithTableState
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 12;

    protected $paginationTheme = 'tailwind';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}