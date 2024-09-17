<?php

namespace App\Http\Livewire\Utilisateur;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Verify extends Component
{
    public function render()
    {
        Auth::user()->sendEmailVerificationNotification();
        return view('livewire.utilisateur.verify')
        ->layout('layouts.verify');
    }
}
