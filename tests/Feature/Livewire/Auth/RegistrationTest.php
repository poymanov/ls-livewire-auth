<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Http\Livewire\Auth\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const BASE_URL = '/registration';

    /**
     * Страница отображает правильный компонент
     */
    function test_page_contains_livewire_component()
    {
        $this->get(self::BASE_URL)->assertSeeLivewire(Registration::getName());
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
     * Отображает форму для ввода регистрационных данных
     */
    public function test_form()
    {
        $response = $this->get(self::BASE_URL);

        $response->assertSee('Email');
        $response->assertSee('Имя');
        $response->assertSee('Пароль');
        $response->assertSee('Подтверждение пароля');
    }

    /**
     * Попытка регистрации с пустыми данными
     */
    public function test_validation_empty()
    {
        Livewire::test(Registration::class)
            ->call('submit')
            ->assertHasErrors([
                'name',
                'email',
                'password',
            ]);
    }

    /**
     * Попытка регистрации с некорректным email
     */
    public function test_validation_not_valid_email()
    {
        Livewire::test(Registration::class)
            ->set('email', 'test')
            ->call('submit')
            ->assertHasErrors([
                'email' => 'email',
            ]);
    }

    /**
     * Попытка регистрации с уже существующим email
     */
    public function test_validation_existed_email()
    {
        $user = User::factory()->create(['email' => 'test@test.ru']);

        Livewire::test(Registration::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasErrors([
                'email' => 'unique',
            ]);
    }

    /**
     * Попытка регистрации со слишком коротким паролем
     */
    public function test_validation_too_short_password()
    {
        Livewire::test(Registration::class)
            ->set('password', '123')
            ->call('submit')
            ->assertHasErrors([
                'password' => 'min',
            ]);
    }

    /**
     * Попытка регистрации со слишком длинным именем
     */
    public function test_validation_too_long_name()
    {
        Livewire::test(Registration::class)
            ->set('name', $this->faker->realText(1000))
            ->call('submit')
            ->assertHasErrors([
                'name' => 'max',
            ]);
    }

    /**
     * Попытка регистрации со слишком длинным email
     */
    public function test_validation_too_long_email()
    {
        Livewire::test(Registration::class)
            ->set('email', $this->faker->realText(1000))
            ->call('submit')
            ->assertHasErrors([
                'email' => 'max',
            ]);
    }

    /**
     * Попытка регистрации с неправильным подтверждением пароля
     */
    public function test_validation_not_valid_password_confirmation()
    {
        Livewire::test(Registration::class)
            ->set('password', '123')
            ->set('password_confirmation', '123qwe')
            ->call('submit')
            ->assertHasErrors([
                'password' => 'confirmed',
            ]);
    }

    /**
     * Успешная регистрация
     */
    public function test_success()
    {
        $name = 'test';
        $email = 'test@test.ru';

        Livewire::test(Registration::class)
            ->set('name', $name)
            ->set('email',$email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }
}
