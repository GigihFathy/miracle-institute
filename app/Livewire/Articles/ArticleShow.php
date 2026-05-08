<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;

class ArticleShow extends Component
{
    public Article $article;

    public function mount(Article $article)
    {
        abort_unless($article->status === 'active', 404);
    }

    public function render()
    {
        $related = Article::where('status', 'active')
            ->where('id', '!=', $this->article->id)
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.frontend.article-show', [
            'related' => $related,
        ])->layout('layouts.learning');
    }
}