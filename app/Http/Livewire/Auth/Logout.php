<?php

declare(strict_types=1);

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function logout()
    {
        Auth::logout();
        return redirect(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
