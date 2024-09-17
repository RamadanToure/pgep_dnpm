<?php

namespace App\Http\Livewire\Annotation;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{Demande, Role, TypeDemande, DemandeEtape, EtapeTypeDemande, Utilisateur, Document, Paiement, Etape, TypePaiement, Service};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Services\GestionnaireEmail;
use App\Payment\OrangeMoney;
use App\Jobs\ProcessusPaiementJob;
use Illuminate\Support\Facades\Bus;

class Index extends Component
{

    use WithFileUploads;
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    public $demande;
    public $selectService;

    public $messages = [];
    public $message;

    public function render()
    {
        return view('livewire.annotation.index');
    }

    public function mount($demande)
    {
        $this->demande = Demande::whereUuid($demande)->first();
        $this->selectService = $this->demande->getServices()->whereNotIn('id', function ($query) {
            $query->from("service")->whereUtilisateurId(auth()->user()->id)->select("id")->get();
        })->first();
        if($this->selectService) {
            $this->messages = $this->selectService->getMessages($this->demande);
        }
    }

    function setCurrentService($uuid) {
        $this->selectService = Service::whereUuid($uuid)->first();

        $this->messages = $this->selectService->getMessages($this->demande);
    }

    function saveMessage() {

        $this->demande->transmettre(
            \auth()->user()->services()->first()->id,
            $this->selectService->id,
            true
        )->update([
            'note' => $this->message
        ]);

        $this->message = '';
        $this->messages = $this->selectService->getMessages($this->demande);
    }
}
