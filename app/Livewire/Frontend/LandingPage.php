<?php

namespace App\Livewire\Frontend;

use App\Models\Article;
use App\Models\Course;
use Livewire\Component;

class LandingPage extends Component
{
    public function render()
    {
        $courses = Course::where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $articles = Article::where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.frontend.landing-page', [
            'courses' => $courses,
            'articles' => $articles,
        ]);
    }
}
