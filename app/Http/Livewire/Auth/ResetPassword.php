<?php

declare(strict_types=1);

namespace App\Http\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $token;

    protected $queryString = ['email'];

    protected $rules = [
        'token'    => 'required',
        'email'    => 'required|email|exists:users,email',
        'password' => 'required|string|confirmed|min:8',
    ];

    public function mount($token)
    {
        $this->token = $token;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('alert.success', Lang::get('auth.reset.successful'));

            return redirect(route('auth.login'));
        } else {
            session()->flash('alert.error', Lang::get('auth.reset.failed'));

            return redirect(route('home'));
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
