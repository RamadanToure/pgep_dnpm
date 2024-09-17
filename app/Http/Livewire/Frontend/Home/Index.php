<?php

namespace App\Http\Livewire\Frontend\Home;

use Livewire\Component;
use App\Models\{Service, TypeDemande};

class Index extends Component
{
    public function render()
    {
        return view('livewire.frontend.home.index', [
            'services' => Service::get(),
            'types' => TypeDemande::orderByRaw('CHAR_LENGTH(nom) ASC')->get()
        ])->layout('layouts.home');
    }
}
