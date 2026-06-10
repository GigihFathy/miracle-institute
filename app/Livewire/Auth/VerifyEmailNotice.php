<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Throwable;

class VerifyEmailNotice extends Component
{
    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->to(localized_route('dashboard'));
        }

        try {
            auth()->user()->sendEmailVerificationNotification();
            session()->flash('status', __('auth.verification_link_resent'));
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('warning', __('auth.mail_unavailable'));
        }
    }

    public function render()
    {
        return view('livewire.auth.verify-email-notice')->layout('layouts.guest');
    }
}
