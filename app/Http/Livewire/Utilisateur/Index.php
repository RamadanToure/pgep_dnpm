<?php

namespace App\Http\Livewire\Utilisateur;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\{Utilisateur, Role};
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Hash;


class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';

    public $itemId, $updateItem = false, $addItem = true;

    //Proprietes
    public $nom, $prenom, $email, $telephone, $role;

    //Controlle d'acces
    //$this->authorize('update', $this->item);

    protected $listeners = [
        'deletePostListner'=>'deletePost'
    ];

    protected $rules = [
        'nom' => 'required',
        'prenom' => 'required',
        'email' => 'required',
        'telephone' => 'required',
        'role' => 'required'
    ];

    public function render()
    {
        authorize('view_user');

        return view('livewire.utilisateur.index', [
            'items' => Utilisateur::paginate(10),
            'roles' => Role::get()
        ]);
    }

    /**
     * Reseting all inputted fields
     * @return void
     */
    public function resetFields(){
        $this->nom = '';
        $this->prenom = '';
        $this->email = '';
        $this->telephone = '';
        $this->role = '';
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

        authorize('create_user');

        try {

            $user = Utilisateur::create([
                'uuid' => Str::uuid(),
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'role_id' => Role::whereUuid($this->role)->first()->id,
                'password' => Hash::make(123456),
            ]);
            $token_update_password = Str::uuid();
            $user->token_update_password = $token_update_password;
            // $user->date_validated_token_password = $date1;

            $user->save();
            session()->flash('success', 'Utilisateur créé avec succès !!');

            $this->resetFields();

            $this->addItem = true;
            $url = request()->getSchemeAndHttpHost()."/get_new_password_user_active/".$token_update_password;

             saveMailToDatabase(
            $user,
            [
                "objet" => "REUNITIALISATION DE MOT DE PASSE",
                "url" => [
                    'link' => $url,
                    'btn' => "Nouveau mot de passe",
                ],
                "message" => message_email("reunitialise_password"),
            ]
        );

        } catch (\Exception $ex) {
            dd($ex);
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

            $item = Utilisateur::whereUuid($uuid)->first();

            if(!$item) {

                session()->flash('error','Utilisateur non trouvé');

            } else {
                $this->nom = $item->nom;
                $this->prenom = $item->prenom;
                $this->email = $item->email;
                $this->telephone = $item->telephone;
                $this->role = $item->role ? $item->role->uuid:null;

                $this->itemId = $item->uuid;

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
        $this->validate();

        authorize('edit_user');

        try {
            //Mise ajour

            Utilisateur::whereuuid($this->itemId)->first()->update([
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'role_id' => Role::whereUuid($this->role)->first()->id
            ]);

            $this->alert('success', "L'utilisateur a été mis à jour avec succès !!");
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

        authorize('delete_user');

        try{

            Utilisateur::whereUuid($uuid)->delete();

            $this->alert("success", "Utilisateur supprimé avec succès !!!");

        }catch(\Exception $e){

            session()->flash('error',"Quelque chose ne va pas!!");
        }
    }
}
