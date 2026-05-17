<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $locale;
    public ?string $currentRouteName; 
    public array $currentRouteParams;

    public array $availableLocales = [
        'id' => 'Indonesia',
        'en' => 'English',
    ];

    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale', 'id'));
        
        if (request()->route()) {
            $this->currentRouteName = request()->route()->getName();
            $this->currentRouteParams = request()->route()->parameters();
        }
    }

    public function updatedLocale(string $value)
    {
        if (! array_key_exists($value, $this->availableLocales)) {
            return;
        }

        Session::put('locale', $value);
        Cookie::queue(Cookie::forever('locale', $value));
        App::setLocale($value);

        if ($this->currentRouteName) {
            $parameters = array_merge($this->currentRouteParams, ['locale' => $value]);
            return redirect()->route($this->currentRouteName, $parameters);
        }

        return redirect()->to('/'.$value);
    }

    public function render()
    {
        return view('livewire.shared.language-switcher');
    }
}