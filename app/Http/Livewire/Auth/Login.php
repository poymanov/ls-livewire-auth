<?php

declare(strict_types=1);

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Services\Auth\VerifyEmailService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class Login extends Component
{
    private const MAX_ATTEMPTS = 5;

    public $email;
    public $password;
    public $remember;

    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Авторизация пользователя
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ValidationException
     */
    public function submit()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $this->ensureEmailConfirmed();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], filled($this->remember))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.login.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return redirect(route('home'));
    }

    /**
     * Повторная отправка письма с подтверждением учетной записи
     */
    public function resendVerifyEmail()
    {
        /** @var VerifyEmailService $verifyEmailService*/
        $verifyEmailService = App::make(VerifyEmailService::class);

        try {
            $verifyEmailService->resendVerifyEmail($this->email);
        } catch (Throwable $e) {
            session()->flash('alert.error', $e->getMessage());
            return redirect(route('login'));
        }

        session()->flash('alert.success', Lang::get('mail.verification.resend.successful'));
        return redirect(route('home'));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.login.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Проверка, что указанный email подтвержден
     */
    private function ensureEmailConfirmed()
    {
        $user = User::where('email', $this->email)->firstOrFail();

        if (!$user->hasVerifiedEmail()) {
            RateLimiter::hit($this->throttleKey());

            session()->flash('login.not.verified.email');

            throw ValidationException::withMessages([
                'email' => Lang::get('auth.login.email.not.confirmed'),
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->email.'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
