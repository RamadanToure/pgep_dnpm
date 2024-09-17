<?php

namespace App\Http\Livewire\TraitementDemande;

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

    public $demande, $consentement = false;
    public $files = [];
    public $methodeSelectionner;
    public $orangePaymentUrl;

    public $selectedDocuments = [], $document, $serviceTraitant, $noteTransmission, $confirmationType;

    protected $listeners = [
        'eventTransmettre' => 'handleTransmettre'
    ];

    public $showFirstConfirmation = false;
    public $showFinalConfirmation = false;
    public $note;

    public $showRejetDialog = false;
    public $motifs = [];

    public $documents = [];

    public $loading = false;
    public $paiementErrorMessage;

    public function rejetDocument()
    {

        if(count($this->motifs)) {

            foreach ($this->motifs as $key => $motif) {
                $this->demande->documents()->find($key)->update([
                    'status' => 2,
                    'motif_rejet' => $motif
                ]);
            }

            $this->alert("success", "Documents rejetés avec succès");

            GestionnaireEmail::envoyerEmailRejetDocuments(
                $this->demande->utilisateur,
                $this->demande->documents()->whereStatus(2)->get()
            );

            $this->selectedDocuments = [];
            $this->motifs = [];
            $this->documents = $this->demande->documents;
            $this->dispatchBrowserEvent("closeModal", []);
            return redirect(request()->header('Referer'));
        }

    }

    function validerDocument(){

        if(count($this->selectedDocuments)) {

            foreach ($this->selectedDocuments as $key => $value) {

                $this->demande->documents()->find($value)->update([
                    'status' => 1,
                ]);
            }

            $this->selectedDocuments = [];

            $this->documents = $this->demande->documents;

            if(!$this->demande->documents()->whereStatus(2)->orWhereNotNull('status')->exists()) {
                GestionnaireEmail::envoyerEmailValidationDocuments(
                    $this->demande->utilisateur,
                    $this->demande
                );
            }

            return redirect(request()->header('Referer'));

            $this->alert("success", "Documents validé avec succès");
        }

    }

    public function openRejetDialog()
    {
        $this->showRejetDialog = true;
    }

    public function closeRejetDialo()
    {
        $this->showRejetDialog = false;
    }

    public function render()
    {
        return view('livewire.traitement-demande.index');
    }

    public function showDocument($id)
    {
        $this->document = Document::find($id);
    }

    public function closeModal(){
        $this->dispatchBrowserEvent("closeModal", []);
        $this->document = null;
    }

    public function hydrate()
    {
        //$this->emit('switcheryHydrate');
        $this->emit('dropifyHydrate');
    }

    function selectMethode($methode, $typePaiement, $etape)
    {
        $this->methodeSelectionner = $methode;
        $this->mode_paiement = $this->methodeSelectionner;

        $type = TypePaiement::whereUuid($typePaiement)->first();
        $etape = Etape::whereUuid($etape)->first();

        $this->demande->setCurrentMethode($methode, $etape);

        if($this->mode_paiement == "Bank") {
            return;
        }

        $this->loading = true;

        if($this->mode_paiement == "OM") {

            $om = new OrangeMoney();
            $om->makePayment($this->demande, $type, $etape);

            if($om->getStatus()) {
                return redirect($om->getPaymentUrl());
            } else {
                $this->paiementErrorMessage = $om->getLassErrorMessage();
            }
        }

        $this->loading = false;
    }

    //Verfier si tout les piement sont effectuer
    function checkDemandePaiements() {

        $paiements = Paiement::whereIn('type_paiement_id', function ($query) {
            $query->from('type_paiement')->whereTypeDemandeId($this->demande->type_demande_id)
            ->select("id")->get();
        })->whereStatus(2)->get();

        $status = [];

        foreach ($this->demande->typeDemande->typePaiements as $key => $type) {

            foreach ($paiements as $key => $paiement) {
                if($paiement->typePaiement->is($type)) $status [] = $paiement;
            }
        }

        return count($status) == $this->demande->typeDemande->typePaiements->count();
    }

    public function mount($demande)
    {
        $this->demande = Demande::whereUuid($demande)->first();

        $this->consentement = $this->demande->soumis;
        $this->documents = $this->demande->documents;
    }

    function submitDemande() {
        $this->demande->update(['soumis' => true]);

        $this->demande->validStep();

        //change step
        $this->demande->changeStep();

        return redirect(request()->header('Referer'));

    }

    public function confirBankPaiement($paiement)
    {

        $paiement = Paiement::whereUuid($paiement)->first();

        $paiement->update([
            'status' => 2,
            'date_status' => now()
        ]);

        $this->demande->validStep();
        $this->demande->changeStep();

        $this->demande->changeStatus(2);

        $this->alert("success", "Paiement validé avec succès");

        $ordre = DemandeEtape::whereDemandeId($paiement->demande->id)
            ->whereEtapeId($paiement->etape->id)->first()->ordre;

        $this->dispatchBrowserEvent('changeStep', ['index' => $ordre]);

        GestionnaireEmail::envoyerEmailConfirmationPaiement(
            $this->demande->utilisateur,
            $paiement
        );
    }

    function rejectBankPaiement($data) {

        Paiement::whereUuid($data['paiement'])->update([
            'status' => 3,
            'date_status' => now(),
            'note' => $data['note']
        ]);

        $this->demande->changeStatus(2);

        $this->alert("success", "Le paiement a été validé avec succès");

    }

    function reprendrePaiement($paiement) {

        Paiement::whereUuid($paiement)->delete();

        return redirect(request()->header('Referer'));
    }

    function sendRecu($etape, $type) {

        $etape = Etape::whereUuid($etape)->first();
        $type = TypePaiement::whereUuid($type)->first();

        Paiement::create([
            'uuid' => Str::uuid(),
            'montant' => $type->montant,
            'date_paiement' => now(),
            'demande_id' => $this->demande->id,
            'type_paiement_id' => $type->id,
            'etape_id' => $etape->id
        ]);

        $this->alert("success", "Votre reçu a été envoyé avec succès");
    }

    function transmission(){

        $this->rules = [
            'serviceTraitant' => 'required',
        ];

        $this->validate();

        $this->emit('eventTransmettre', [
            "note" => $this->noteTransmission,
            "service" => $this->serviceTraitant
        ]);

        $this->noteTransmission = null;
        $this->serviceTraitant = null;

        $this->alert("success", "Demande transmis avec succès");
        $this->dispatchBrowserEvent("closeModal");

    }

    public function handleTransmettre($data)
    {
        $this->demande->transmettre(
            $this->demande->typeDemande->service->id,
            Service::whereUuid($data['service'])->first()->id,
            false
        )->update([
            'note' => $data['note']
        ]);
    }

    function initProjetAgrement() {
        $this->demande->validStep();
        //change step
        $this->demande->changeStep();

        return redirect(request()->header('Referer'));
    }

    function changeStatus($status) {

        $this->demande->update([
            'niveau' => $status
        ]);

        if($status == 1) {
            $this->alert("success", "Demande transmis avec succès");
        }
    }

}
