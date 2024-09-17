<?php

namespace App\Http\Livewire\TypeService;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{TypeService, Role};
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
    public $nom;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'nom' => 'required'
    ];

    public function render()
    {
        return view('livewire.type-service.index', [
            'items' => TypeService::paginate(10),
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
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

            TypeService::create([
                'uuid' => Str::uuid(),
                'nom' => $this->nom
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

            $item = TypeService::whereUuid($uuid)->first();

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

            TypeService::whereuuid($this->itemId)->first()->update([
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

            TypeService::whereUuid($uuid)->delete();

            $this->alert("success", "Type service supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
