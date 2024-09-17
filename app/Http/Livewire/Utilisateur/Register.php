<?php

namespace App\Http\Livewire\Utilisateur;

use Livewire\Component;
use Illuminate\Auth\Events\Registered;
use App\Models\{Utilisateur, Role, Service, TypeDemande, Demande};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Auth;

class Register extends Component
{

    public $prenom;
    public $nom;
    public $telephone;
    public $email;
    public $adresse;
    public $cgu_accepted;

    public $password;
    public $password_confirmation;

    public $select_type = '';

    //= "12345678"
    //= "12345678"

    protected $rules = [
        'prenom' => 'required',
        'nom' => 'required',
        'telephone' => 'required|phone:GN,AUTO|unique:utilisateur',
        'email' => 'required|email|unique:utilisateur',
        'adresse' => 'required',
        "select_type" => 'required',
        'cgu_accepted' => 'required|accepted',
    ];

    protected $messages = [
        'cgu_accepted.required' => "Vous devez accepter les termes et conditions d'utilisation",
    ];

    public function render()
    {
        return view('livewire.utilisateur.register', [
            'services' => Service::whereIn('id', function ($query) {
                $query->from("type_demande")->select("service_id")->get();
            })->get()
        ])
        ->layout('layouts.register');
    }

    public function hydrate()
    {
        $this->emit('select2Hydrate');
    }

    public function submitForm()
    {
        $this->validate();

        // Traitement ou sauvegarde des données ici

        $utilisateur = Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'role_id' => Role::whereNom("demandeur")->first()->id,
        ]);

        $type = TypeDemande::whereUuid($this->select_type)->first();

        $numero = generated_demande_number(5);

        $demande = Demande::create([
            'uuid' => Str::uuid(),
            'etape_id' => $type->getFirstStep()->etape->id,
            'type_demande_id' => $type->id,
            'utilisateur_id' => $utilisateur->id,
            'status' => 1,
            'numero_demande' => $numero
        ]);

        //Initialiser la demande avec les etapes du type
        $demande->initStep();

        // Informations de la demande
        $nomDemandeur = $utilisateur->getName();
        $dateDemande = dateFormat($demande->created_at);
        $typeNom = $type->nom;

        // Construction du message SMS
        $message = "Bonjour $nomDemandeur, votre inscription est confirmée ! Votre demande d'agrément (Numéro : $numero) a été initiée. Veuillez compléter les détails requis dans le formulaire, le soumettre, puis procéder au paiement. Vous pourrez suivre l'état de votre demande en utilisant ce numéro. Détails : Nom: $nomDemandeur, Type: $typeNom, Date: $dateDemande. Nous vous tiendrons informé(e) de son avancement. Merci, Votre service.";

        send_sms($message, $utilisateur->telephone);

        //Trasmettre la demande au chef service
        $demande->transmettre(
            null,
            $type->service->id,
            true
        );

        //Connecter le demandeur
        Auth::login($utilisateur);

        //redirection
        return redirect()->to("/demande/{$demande->uuid}");

    }
}
