<?php

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Registration;
use App\Http\Livewire\Home;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', Home::class)->name('home');

Route::group(['as' => 'auth.', 'middleware' => 'guest'], function () {
    Route::get('/registration', Registration::class)->name('registration');
    Route::get('/login', Login::class)->name('login');
});
