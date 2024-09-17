<?php

namespace App\Http\Livewire\Service;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{Service, Utilisateur, TypeService};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';

    public $itemId, $updateItem = false, $addItem = true, $isDivision = false, $isCentral = false;

    //Proprietes
    public $nom, $utilisateur, $typeService, $serviceParent;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'nom' => 'required',
        'typeService' => 'required',
        'utilisateur' => 'required',
    ];

    public function render()
    {
        return view('livewire.service.index', [
            'items' => Service::whereNull('parent_id')->paginate(10),
            'utilisateurs' => Utilisateur::get(),
            'services' => Service::get(),
            'types' => TypeService::get()
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
        $this->utilisateur = '';
        $this->typeService = '';
        $this->serviceParent = '';
        $this->isDivision = false;
        $this->isCentral = false;
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

        if($this->isDivision) {

            $this->rules = [
                'nom' => 'required',
                'typeService' => 'required',
                'utilisateur' => 'required',
                'serviceParent' => 'required',
            ];
        }

        $this->validate();

        try {

            $service = Service::create([
                'uuid' => Str::uuid(),
                'nom' => $this->nom,
                'is_central' => $this->isCentral,
                'utilisateur_id' => Utilisateur::whereUuid($this->utilisateur)->first()->id,
                'type_service_id' => TypeService::whereUuid($this->typeService)->first()->id
            ]);

            if($this->isDivision) {
                $service->update([
                    'parent_id' => Service::whereUuid($this->serviceParent)->first()->id
                ]);
            }

            $this->alert('success', 'Service créé avec succès !!');

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
        try {

            $item = Service::whereUuid($uuid)->first();

            if(!$item) {

                session()->flash('error','Service non trouvé');

            } else {
                $this->nom = $item->nom;
                $this->utilisateur = $item->utilisateur->uuid ?? null;
                $this->typeService = $item->typeService->uuid;
                $this->isCentral = $item->is_central;

                $this->itemId = $item->uuid;

                $this->isDivision = $item->service ? true:false;
                $this->serviceParent = $item->service ? $item->service->uuid:null;

                $this->updateItem = true;
                $this->addItem = false;
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
        if($this->isDivision) {

            $this->rules = [
                'nom' => 'required',
                'typeService' => 'required',
                'utilisateur' => 'required',
                'serviceParent' => 'required',
            ];
        }

        $this->validate();

        try {
            //Mise ajour
            Service::whereuuid($this->itemId)->first()->update([
                'nom' => $this->nom,
                'is_central' => $this->isCentral,
                'utilisateur_id' => Utilisateur::whereUuid($this->utilisateur)->first()->id,
                'parent_id' => Service::whereUuid($this->serviceParent)->first()->id ?? null,
                'type_service_id' => TypeService::whereUuid($this->typeService)->first()->id
            ]);

            $this->alert('success', "Le service a été mis à jour avec succès !!");
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

            Service::whereUuid($uuid)->delete();

            $this->alert("success", "Service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
