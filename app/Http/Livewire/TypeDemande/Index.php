<?php

namespace App\Http\Livewire\TypeDemande;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{TypeDemande, Service};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';

    public $itemId, $updateItem = false, $addItem = true;

    //Proprietes
    public $nom, $service;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'nom' => 'required',
        'service' => "required"
    ];

    public function render()
    {
        return view('livewire.type-demande.index', [
            'items' => TypeDemande::paginate(10),
            'services' => Service::whereNotIn('type_service_id', function ($query) {
                $query->from("type_service")->whereNom("Service central")->select("id")->get();
            })->whereNull("parent_id")->get()
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
        $this->service = '';
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

            TypeDemande::create([
                'uuid' => Str::uuid(),
                'nom' => $this->nom,
                'service_id' => Service::whereUuid($this->service)->first()->id
            ]);

            session()->flash('success', 'Type service créé avec succès !!');

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

            $item = TypeDemande::whereUuid($uuid)->first();

            if(!$item) {

                $this->alert('error','Type service non trouvé');

            } else {
                $this->nom = $item->nom;
                $this->service = $item->service->uuid;

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

            TypeDemande::whereuuid($this->itemId)->first()->update([
                'nom' => $this->nom,
                'service_id' => Service::whereUuid($this->service)->first()->id
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

            TypeDemande::whereUuid($uuid)->delete();

            $this->alert("success", "Type service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
