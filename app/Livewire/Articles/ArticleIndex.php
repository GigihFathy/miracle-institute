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
        $articles = Article::where('status', 'active')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
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