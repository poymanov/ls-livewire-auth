<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Registration;
use App\Http\Livewire\Auth\ResetPassword;
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
    Route::get('/forgot-password', ForgotPassword::class)->name('password.forgot');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['guest', 'signed', 'throttle:6,1'])
    ->name('verification.verify');


