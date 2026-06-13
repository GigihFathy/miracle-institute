<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Throwable;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|min:2|max:35',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function updated($propertyName): void
    {
        if ($propertyName === 'name') {
            $this->name = mb_substr((string) $this->name, 0, 35);
        }

        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $studentRole = Role::where('name', 'student')->firstOrFail();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id, [
                'assigned_at' => now(),
            ]);
        }

        Auth::login($user);
        try {
            $user->sendEmailVerificationNotification();
            session()->flash('status', __('auth.verification_email_sent'));
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('warning', __('auth.mail_unavailable'));
        }

        return redirect()->to(localized_route('verification.notice'));
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest')->layout('layouts.guest');
    }
}
