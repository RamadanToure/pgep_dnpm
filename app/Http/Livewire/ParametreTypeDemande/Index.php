<?php

namespace App\Http\Livewire\ParametreTypeDemande;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{TypeDemande, Service, Etape, EtapeTypeDemande, TypePaiement, TypeDocument, TypeDemandeTypeDocumentEtape};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    public $itemId, $updateItem = false, $addItem = true, $typeDemande, $typePaiement, $select_documents = [];

    public $havePaiement = false;
    public $isMobilePaiement = false;
    public $isTraitement = false;

    //Proprietes
    public $nom, $service, $montant;

    //nombre de documents par etape
    public $countDoc = 0;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'sortListListner' => 'sortLits'
    ];

    protected $rules = [
        'nom' => 'required',
        'havePaiement' => 'nullable'
    ];

    public function hydrate()
    {
        $this->emit('select2Hydrate');
    }

    public function mount($type)
    {
        $this->typeDemande = TypeDemande::whereUuid($type)->first();
    }

    public function sortLits($data)
    {
        EtapeTypeDemande::whereUuid($data['item'])
        ->update(['ordre' => $data['ordre']]);
    }

    public function render()
    {
        return view('livewire.parametre-type-demande.index', [
            'type' => $this->typeDemande,
            'etapes' => $this->typeDemande->etapes()->orderByPivot("ordre")->get(),
            'documents' => TypeDocument::orderBy('nom')->get(),
            'typePaiements' => $this->typeDemande->typePaiements
        ]);
    }

    public function getCountDoc($etape_id)
    {
         return TypeDemandeTypeDocumentEtape::where('type_demande_id', '=', $this->typeDemande->id)->where('etape_id', '=', $etape_id)->count();
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
        $this->select_documents = [];
        $this->typePaiement = '';
        $this->havePaiement = false;
    }

    /**
     * Open Add Item form
     * @return void
     */
    public function addPost()
    {
        $this->resetFields();
        $this->addItem = true;
        $this->updateItem = false;
    }

    function createPaiement() {

        $type = TypePaiement::create([
            'nom' => $this->nom,
            'type_demande_id' => $this->typeDemande->id
        ]);

        if(!$type->uuid) $type->update(['uuid' => Str::uuid()]);

        if($this->montant) $type->update(['montant' => $this->montant]);

        return $type;
    }

    /**
      * store the user inputted post data in the posts table
      * @return void
    */
    public function storeItem()
    {
        $this->validate();

        try {

            $etape = Etape::create([
                'uuid' => Str::uuid(),
                'nom' => $this->nom,
            ]);

            $etapeType = EtapeTypeDemande::create([
                'uuid' => Str::uuid(),
                'etape_id' => $etape->id,
                'is_mobile_paiement' => $this->isMobilePaiement,
                'type_demande_id' => $this->typeDemande->id,
                'is_traitement' => $this->isTraitement
            ]);

            foreach ($this->select_documents as $key => $value) {

                TypeDemandeTypeDocumentEtape::firstOrCreate([
                    'etape_id' => $etape->id,
                    'type_demande_id' => $this->typeDemande->id,
                    'type_document_id' => TypeDocument::whereUuid($value)->first()->id
                ]);
            }

            if($this->havePaiement) {

                $etapeType->update([
                    'type_paiement_id' => $this->createPaiement()->id
                ]);
            }

            $this->alert('success', 'Type service créé avec succès !!');

            $this->resetFields();

            $this->addItem = true;

        } catch (\Exception $ex) {

            session()->flash('error','Quelque chose ne va pas!!'.$ex->getMessage());
        }
    }

    /**
     * show existing item data in edit item form
     * @param mixed $id
     * @return void
     */
    public function editItem($uuid){

        $this->resetFields();

        try {

            $item = EtapeTypeDemande::whereUuid($uuid)->first();

            if(!$item) {

                $this->alert('error','Type demande non trouvé');

            } else {
                $this->nom = $item->etape->nom;
                $this->itemId = $item->uuid;

                $this->updateItem = true;
                $this->addItem = false;

                $this->havePaiement = $item->typePaiement != null;
                $this->isTraitement = $item->is_traitement;
                if($this->havePaiement) $this->typePaiement = $item->typePaiement->uuid;
                if($this->havePaiement) $this->montant = $item->typePaiement->montant;

                $this->isMobilePaiement = $item->is_mobile_paiement;

                if($item->typeDemande->typeDocuments) {

                    $types = TypeDemandeTypeDocumentEtape::whereTypeDemandeId($this->typeDemande->id)
                    ->whereEtapeId($item->etape->id)->get();

                    foreach ($types as $key => $value) {
                        $this->select_documents[] = $value->typeDocument->uuid;
                    }
                }

            }
        } catch (\Exception $ex) {
            session()->flash('error','Something goes wrong!!'.$ex->getMessage());
        }
    }

    /**
     * update the item data
     * @return void
     */
    public function updateItem()
    {
        $this->validate();

        try {

            $etape = EtapeTypeDemande::whereUuid($this->itemId)->first();

            $etape->etape->update([
                'nom' => $this->nom,
            ]);

            $etape->update([
                'is_traitement' => $this->isTraitement
            ]);

            TypeDemandeTypeDocumentEtape::whereTypeDemandeId($this->typeDemande->id)->whereEtapeId($etape->etape->id)->delete();

            foreach ($this->select_documents as $key => $value) {

                TypeDemandeTypeDocumentEtape::firstOrCreate([
                    'etape_id' => $etape->etape->id,
                    'type_demande_id' => $this->typeDemande->id,
                    'type_document_id' => TypeDocument::whereUuid($value)->first()->id,
                ]);
            }

            if($this->havePaiement) {
                $etape->update([
                    'type_paiement_id' => $this->createPaiement()->id,
                    'is_mobile_paiement' => $this->isMobilePaiement,
                ]);
            } else {
                $etape->update([
                    'type_paiement_id' => null,
                    'is_mobile_paiement' => false
                ]);
            }

            $this->alert('success', "L'etape a été mis à jour avec succès !!");
            $this->resetFields();

            $this->updateItem = false;
            $this->addItem = true;
        } catch (\Exception $ex) {

            session()->flash('success', 'Quelque chose ne va pas!!'.$ex->getMessage());
        }
    }

    /**
     * delete specific post data from the posts table
     * @param mixed $id
     * @return void
     */
    public function deleteItem($uuid)
    {
        try{

            Etape::whereUuid($uuid)->delete();

            $this->alert("success", "Type service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
