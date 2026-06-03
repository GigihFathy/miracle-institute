<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class TopbarCourseSearch extends Component
{
    public string $search = '';

    public function mount(): void
    {
        $this->search = trim((string) request()->query('search', ''));
    }

    public function render()
    {
        return view('livewire.shared.topbar-course-search');
    }
}
