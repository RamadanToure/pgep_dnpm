<?php

namespace App\Http\Livewire\Utilisateur;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\{Demande};

class Login extends Component
{

    public $email;
    public $password;

    public $suivi;

    public $numero_demande;
    public $code_sms;
    public $checkCode = false;
    public $demande;

    public function mount($suivi = false)
    {
        $this->suivi = $suivi;
    }

    public function render()
    {
        return view('livewire.utilisateur.login')->layout('layouts.login');
    }

    function suivreDemande($status) {
        $this->suivi = $status == "2";
    }

    public function login()
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {
            // Connexion réussie, rediriger ou faire quelque chose d'autre
            return redirect("/home");
        } else {
            // Échec de la connexion, afficher un message d'erreur
            session()->flash('error', 'Identifiants invalides.');
        }
    }

    function suiviDemande() {

        if($this->checkCode) {

            $this->validate([
                'numero_demande' => 'required|min:5',
                'code_sms' => "required"
            ]);

            $this->demande = Demande::whereNumeroDemande($this->numero_demande)
            ->whereCodeSms($this->code_sms)
            ->first();

            if(!$this->demande) {
                session()->flash('error', 'Code sms invalide.');
                return;
            }

            Auth::login($this->demande->utilisateur);

            $this->demande->update([
                'code_sms' => null
            ]);

            return redirect()->to("/demande/{$this->demande->uuid}");

        }

        $this->validate([
            'numero_demande' => 'required|min:5',
        ]);

        $this->demande = Demande::whereNumeroDemande($this->numero_demande)->first();

        if(!$this->demande) {
            session()->flash('error', 'Numero de demande invalide.');
            return;
        }

        $code = \generate_code(4);

        $this->demande->update([
            'code_sms' => $code
        ]);

        $message = "$code est votre code verification pour suivre votre demande";

        \send_sms($message, $this->demande->utilisateur->telephone);

        session()->flash('success', 'Vous allez recevoir un sms avec un code pour acceder a votre demande, saisissez le dans le champs ci-dessous.');

        $this->checkCode = true;

    }
}
