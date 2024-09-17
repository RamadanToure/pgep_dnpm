<?php

namespace App\Http\Livewire;

use App\Models\Role;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Livewire\WithFileUploads;

class InscriptionComponent extends Component {

    use WithFileUploads;
    public $nom;
    public $prenom;
    public $telephone;
    public $adresse;
    public $email;
    public $document;
    public function render() {
        return view('auth.inscription');
    }
    public function save_inscription(Request $request) {

        $this->validate([
            'prenom' => ['required','min:2'],
            'nom' => ['required','min:2'],
            'email' => "required|email|unique:utilisateur,email",
            'telephone' => "required|phone:GN|unique:utilisateur,telephone",
            'adresse' => ['required','min:2'],
        ]);

        $phone = new PhoneNumber($this->telephone, 'GN');

        $request->merge(['telephone' => $phone->formatE164()]);

        $user = new Utilisateur();
        $user->uuid = Str::uuid();
        $user->nom = $this->nom;
        $user->prenom = $this->prenom;
        $user->telephone = $this->telephone;
        $user->adresse = $this->adresse;
        $user->email = $this->email;
        $user->password = Hash::make(1234);
        $user->is_valide = false;
        $user->status_compte = true;
        $user->is_deleted = 0;
        $user->role_id = Role::whereNom("demandeur")->first()->id;

        $date1 = Carbon::createFromFormat('d-m-Y H:i:s',Carbon::now()->format("d-m-Y H:i:s"));

        $token_update_password = Str::uuid();
        $user->token_update_password = $token_update_password;
        $user->date_validated_token_password = $date1;

        $user->save();

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

        $user->save();

        return redirect('login')->with('message_success', "Votre demande de création de compte a été enregistrée avec succès. Veuillez patienter le temps que votre demande soit validée.");

    }

}
