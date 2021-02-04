<?php

declare(strict_types=1);

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|string|email|exists:users,email',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => Lang::get('auth.forgot.throttle'),
            ]);
        } else {
            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('alert.success', Lang::get('auth.forgot.successful'));

                return redirect(route('home'));
            } else {
                session()->flash('alert.error', Lang::get('auth.forgot.failed'));

                return redirect(route('home'));
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
