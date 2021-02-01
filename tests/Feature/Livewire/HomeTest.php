<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Home;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Страница отображает правильный компонент
     */
    function test_page_contains_livewire_component()
    {
        $this->get('/')->assertSeeLivewire(Home::getName());
    }

    /**
     * Для неавторизованного пользователя отображаются ссылки для регистрации/авторизации
     */
    public function test_guest_page_contains_auth_links()
    {
        $response = $this->get('/');
        $response->assertSee('Регистрация');
    }
}
