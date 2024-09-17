<?php

namespace App\Http\Livewire\Demande;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{Demande, Role, TypeDemande, Utilisateur};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    public $itemId, $updateItem = false, $addItem = true;

    //Proprietes
    public $typeDemande, $status = 0;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'typeDemande' => 'required'
    ];

    public function mount($status)
    {
        $this->status = $status;
    }

    public function render()
    {

        if(\auth()->user()->isDemandeur()) {
            $demandes = auth()->user()->demandes();
        } else if(\auth()->user()->isService()) {
            $demandes = \auth()->user()->getDemandesByService();
        } else {
            $demandes = Demande::whereNull('id');
        }

        return view('livewire.demande.index', [
            'items' => $demandes->whereStatus($this->status)->orderBy("created_at", "DESC")->paginate(10),
            'types' => TypeDemande::get(),
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->typeDemande = '';
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

        $type = TypeDemande::whereUuid($this->typeDemande)->first();

        if(!$type || !$type->etapes->count()) {
            return $this->alert("error", "Erreur");
        }

        try {

            $demande = Demande::create([
                'uuid' => Str::uuid(),
                'etape_id' => $type->getFirstStep()->etape->id,
                'type_demande_id' => $type->id,
                'utilisateur_id' => \auth()->user()->id,
                'status' => 1
            ]);

            //Initialiser la demande avec les etapes du type
            $demande->initStep();

            //Trasmettre la demande ax chef service
            $demande->transmettre(
                null,
                $type->service->id,
                true
            );

            //redirection
            return redirect()->to("/demande/{$demande->uuid}");

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

            $item = Demande::whereUuid($uuid)->first();

            if(!$item) {

                session()->flash('error','Type service non trouvé');

            } else {
                $this->nom = $item->nom;

                $this->itemId = $item->uuid;

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

            Demande::whereuuid($this->itemId)->first()->update([
                'nom' => $this->nom
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

            Demande::whereUuid($uuid)->delete();

            $this->alert("success", "Type service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
