<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Throwable;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:8',
        'remember' => 'boolean',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            $this->addError('email', 'Email atau password salah.');

            return;
        }

        if (! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
                session()->flash('status', __('auth.verification_required'));
            } catch (Throwable $exception) {
                report($exception);
                session()->flash('warning', __('auth.mail_unavailable'));
            }

            return redirect()->to(localized_route('login'));
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();

        return redirect()->intended(localized_route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
