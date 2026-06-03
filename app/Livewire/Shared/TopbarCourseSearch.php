<?php

namespace App\Livewire\Shared;

use App\Models\Course;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopbarCourseSearch extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function mount(): void
    {
        $this->search = trim((string) request()->query('search', ''));
    }

    public function render()
    {
        $term = trim($this->search);

        /** @var Collection<int, \App\Models\Course> $results */
        $results = collect();

        if ($term !== '') {
            $search = '%' . $term . '%';

            $results = Course::query()
                ->with('studyProgram')
                ->withCount('topics')
                ->where('status', 'active')
                ->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('title', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhereHas('studyProgram', function ($studyProgramQuery) use ($search) {
                            $studyProgramQuery->where('title', 'like', $search);
                        });
                })
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('livewire.shared.topbar-course-search', [
            'results' => $results,
            'hasSearch' => $term !== '',
        ]);
    }
}
