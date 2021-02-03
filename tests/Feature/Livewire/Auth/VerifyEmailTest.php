<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Попытка подтверждения email для несуществующего пользователя
     */
    public function test_not_existed_user()
    {
        $url = $this->getVerificationUrl(99, 'test@test.ru');

        $response = $this->get($url);
        $response->assertRedirect('/');
        $response->assertSessionHas('alert.error', Lang::get('mail.verification.user.unknown'));
    }

    /**
     * Попытка подтверждения уже подтвержденного email
     */
    public function test_already_verified()
    {
        $user = User::factory()->create();
        $url = $this->getVerificationUrl($user->id, $user->email);

        $response = $this->get($url);
        $response->assertRedirect('/');
        $response->assertSessionHas('alert.error', Lang::get('mail.verification.already'));
    }

    /**
     * Попытка подтверждения email с неправильным хэшэм
     */
    public function test_wrong_hash()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $url = $this->getVerificationUrl($user->id, 'test@test.ru');

        $response = $this->get($url);
        $response->assertRedirect('/');
        $response->assertSessionHas('alert.error', Lang::get('mail.verification.hash.not.equal'));
    }

    /**
     * Успешное подтверждение почты
     */
    public function test_success()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $url = $this->getVerificationUrl($user->id, $user->email);

        $response = $this->get($url);
        $response->assertRedirect('/');
        $response->assertSessionHas('alert.success', Lang::get('mail.verification.successful'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email_verified_at' => null,
        ]);
    }

    /**
     * Формирование email для подтверждения почты пользователя
     *
     * @param int    $userId Идентификатор пользователя, email которого необходимо подтвердить
     * @param string $email Адрес, который необходимо подтвердить
     *
     * @return string
     */
    private function getVerificationUrl(int $userId, string $email): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $userId,
                'hash' => sha1($email),
            ]
        );
    }
}
