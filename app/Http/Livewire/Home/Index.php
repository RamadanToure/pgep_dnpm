<?php

namespace App\Http\Livewire\Home;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.home.index', [
            "statistiques" => [
                [
                    'titre' => "Demande en attente de traitement",
                    'icon' => "mdi mdi-clock-outline", // Classe MDI pour l'icône en attente
                    'nombre' => \auth()->user()->statistique(1),
                    'color' => "bg-primary"
                ],
                [
                    'titre' => "Demande en cours de traitement",
                    'icon' => "mdi mdi-progress-clock", // Classe MDI pour l'icône en cours de traitement
                    'nombre' => \auth()->user()->statistique(2),
                    'color' => "bg-success"
                ],
                [
                    'titre' => "Demandes traitées",
                    'icon' => "mdi mdi-check-circle", // Classe MDI pour l'icône des demandes traitées
                    'nombre' => \auth()->user()->statistique(3),
                    'color' => "bg-dark"
                ],
                [
                    'titre' => "Demandes rejetées",
                    'icon' => "mdi mdi-cancel", // Classe MDI pour l'icône des demandes rejetées
                    'nombre' => \auth()->user()->statistique(4),
                    'color' => "bg-danger"
                ],
            ]
        ]);
    }
}
