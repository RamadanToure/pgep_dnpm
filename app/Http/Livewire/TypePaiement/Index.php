<?php

namespace App\Http\Livewire\TypePaiement;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{TypePaiement, TypeDemande};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    public $itemId, $updateItem = false, $addItem = true;

    //Proprietes
    public $nom, $type, $montant;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'nom' => 'required',
        'type' => "required",
        'montant' => "required"
    ];

    public function render()
    {
        return view('livewire.type-paiement.index', [
            'items' => TypePaiement::paginate(10),
            'types' => TypeDemande::get()
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
        $this->type = '';
        $this->montant = '';
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

    /**
      * store the user inputted post data in the posts table
      * @return void
    */
    public function storeItem()
    {
        $this->validate();

        try {

            TypePaiement::create([
                'uuid' => Str::uuid(),
                'nom' => $this->nom,
                'montant' => $this->montant,
                'type_demande_id' => TypeDemande::whereUuid($this->type)->first()->id
            ]);

            $this->alert('success', 'Type paiement créé avec succès !!');

            $this->resetFields();

            $this->addItem = true;

        } catch (\Exception $ex) {

            session()->flash('error','Quelque chose ne va pas!!');
        }
    }

    /**
     * show existing item data in edit item form
     * @param mixed $id
     * @return void
     */
    public function editItem($uuid){
        try {

            $item = TypePaiement::whereUuid($uuid)->first();

            if(!$item) {

                $this->alert('error','Type service non trouvé');

            } else {
                $this->nom = $item->nom;
                $this->type = $item->typeDemande->uuid;
                $this->itemId = $item->uuid;
                $this->montant = $item->montant;

                $this->updateItem = true;
                $this->addItem = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error','Something goes wrong!!');
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

            $typePaiement = TypePaiement::whereuuid($this->itemId)->first();

            $history = collect();

            //Save history
            if($typePaiement->historique) {
                $history = $history->push(json_decode($typePaiement->historique));
            }

            $history->push([
                'montant' => $this->montant,
                'date' => now()
            ]);

            $typePaiement->update([
                'historique' => $history->toJson(),
                'nom' => $this->nom,
                'montant' => $this->montant,
                'type_demande_id' => TypeDemande::whereUuid($this->type)->first()->id
            ]);

            $this->alert('success', "Le type de service a été mis à jour avec succès !!");
            $this->resetFields();

            $this->updateItem = false;
            $this->addItem = true;
        } catch (\Exception $ex) {

            session()->flash('success', 'Quelque chose ne va pas!!');
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

            TypePaiement::whereUuid($uuid)->delete();

            $this->alert("success", "Type service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
