<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

trait WithTableState
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 9;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}