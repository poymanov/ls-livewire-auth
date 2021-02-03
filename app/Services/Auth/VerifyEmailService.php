<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Lang;
use Throwable;

class VerifyEmailService
{
    /**
     * Подтверждение email пользователя
     *
     * @param int    $userId Идентификатор пользователя, адрес которого необходимо подтвердить
     * @param string $emailHash Хэш адреса почты, который необходимо подтвердить
     */
    public function verify(int $userId, string $emailHash): void
    {
        // Если пользователь не найден
        try {
            $user = User::findOrFail($userId);
        } catch (Throwable $e) {
            throw new \Exception(Lang::get('mail.verification.user.unknown'));
        }

        if ($user->hasVerifiedEmail()) {
            throw new \Exception(Lang::get('mail.verification.already'));
        }

        // Неправильный email hash
        if (! hash_equals($emailHash,
            sha1($user->getEmailForVerification()))) {
            throw new \Exception(Lang::get('mail.verification.hash.not.equal'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        } else {
            throw new \Exception(Lang::get('mail.verification.failed'));
        }
    }
}
