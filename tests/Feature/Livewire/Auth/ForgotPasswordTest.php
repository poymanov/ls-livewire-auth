<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Http\Livewire\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL = '/forgot-password';

    /**
     * Страница отображает правильный компонент
     */
    function test_page_contains_livewire_component()
    {
        $this->get(self::BASE_URL)->assertSeeLivewire(ForgotPassword::getName());
    }

    /**
     * Страница доступна только для гостей
     */
    function test_guest_only()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get(self::BASE_URL);
        $response->assertRedirect('/');
    }

    /**
     * Отображает форму для даннных
     */
    public function test_form()
    {
        $response = $this->get(self::BASE_URL);

        $response->assertSee('Email');
    }

    /**
     * Попытка запроса с пустыми данными
     */
    public function test_validation_empty()
    {
        Livewire::test(ForgotPassword::class)
            ->call('submit')
            ->assertHasErrors([
                'email',
            ]);
    }

    /**
     * Попытка запроса с некорректным email
     */
    public function test_validation_not_valid_email()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'test')
            ->call('submit')
            ->assertHasErrors([
                'email' => 'email',
            ]);
    }

    /**
     * Попытка запроса для несуществующего пользователя
     */
    public function test_not_existed_user()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'test@test.ru')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    /**
     * Запрос пароля уже был создан ранее
     */
    public function test_already_requested()
    {
        $user = User::factory()->create();

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => 123,
            'created_at' => now(),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    /**
     * Успешный запрос сброса пароля
     */
    public function test_success()
    {
        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasNoErrors(['email'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);
    }
}
