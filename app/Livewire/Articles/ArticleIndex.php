<?php

namespace App\Livewire\Articles;

use App\Livewire\Concerns\WithTableState;
use App\Models\Article;
use Livewire\Component;

class ArticleIndex extends Component
{
    use WithTableState;

    public function render()
    {
        $articles = Article::query()
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('author', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.frontend.article-index', [
            'articles' => $articles,
        ])->layout('layouts.learning');
    }
}