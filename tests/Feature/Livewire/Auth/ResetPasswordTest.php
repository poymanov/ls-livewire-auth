<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Http\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL = '/reset-password/';

    /**
     * Страница отображает правильный компонент
     */
    function test_page_contains_livewire_component()
    {
        $this->get(self::BASE_URL . '123')->assertSeeLivewire(ResetPassword::getName());
    }

    /**
     * Страница доступна только для гостей
     */
    function test_guest_only()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get(self::BASE_URL . '123');
        $response->assertRedirect('/');
    }

    /**
     * Отображает форму для даннных
     */
    public function test_form()
    {
        $response = $this->get(self::BASE_URL . '123');

        $response->assertSee('Email');
        $response->assertSee('Пароль');
        $response->assertSee('Подтверждение пароля');
    }

    /**
     * Попытка сброса с пустыми данными
     */
    public function test_validation_empty()
    {
        Livewire::test(ResetPassword::class, ['token', 123])
            ->call('submit')
            ->assertHasErrors([
                'email',
                'password',
            ]);
    }

    /**
     * Попытка сброса с некорректным email
     */
    public function test_validation_not_valid_email()
    {
        Livewire::test(ResetPassword::class, ['token', 123])
            ->set('email', 'test')
            ->call('submit')
            ->assertHasErrors([
                'email' => 'email',
            ]);
    }

    /**
     * Попытка сброса со слишком коротким паролем
     */
    public function test_validation_too_short_password()
    {
        Livewire::test(ResetPassword::class, ['token', 123])
            ->set('password', '123')
            ->call('submit')
            ->assertHasErrors([
                'password' => 'min',
            ]);
    }

    /**
     * Попытка сброса с неправильным подтверждением пароля
     */
    public function test_validation_not_valid_password_confirmation()
    {
        Livewire::test(ResetPassword::class, ['token', 123])
            ->set('password', '123')
            ->set('password_confirmation', '123qwe')
            ->call('submit')
            ->assertHasErrors([
                'password' => 'confirmed',
            ]);
    }

    /**
     * Попытка запроса для несуществующего пользователя
     */
    public function test_not_existed_user()
    {
        Livewire::test(ResetPassword::class, ['token', 123])
            ->set('email', 'test@test.ru')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    /**
     * Неправильный токен при сбросе пароля
     */
    public function test_wrong_token()
    {
        $user = User::factory()->create();

        $token = Password::createToken($user);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        Livewire::test(ResetPassword::class, ['token' => 123])
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertRedirect('/');
    }

    /**
     * Успешный запрос сброса пароля
     */
    public function test_success()
    {
        $user = User::factory()->create();

        $token = Password::createToken($user);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect('/login');
    }
}
