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

        $this->article = $article;
    }

    public function render()
    {
        return view('livewire.frontend.article-show', [
            'related' => Article::query()
                ->where('status', 'active')
                ->where('id', '!=', $this->article->id)
                ->latest()
                ->take(4)
                ->get(),
        ])->layout('layouts.learning');
    }
}