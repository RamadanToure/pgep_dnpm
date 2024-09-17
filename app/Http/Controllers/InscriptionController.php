<?php

namespace App\Http\Controllers;

use App\Models\{Utilisateur, Role};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    function createNewPassword($token)
    {
        return view('auth.password', [
            "titre" => "Creation de votre mot de passe",
            'token' => $token,
        ]);
    }

    function setNewPassword(Request $request)
    {
        $rules = [
            'password' => 'required|min:8|confirmed',
            'password_token' => 'required|exists:utilisateur,token_update_password'
        ];

        $customMessages = [
            'required|min:8|confirmed' => 'Le mot de passe doit avoir au minimum 8 chiffres.'
        ];

        $this->validate($request, $rules, $customMessages);

        $user = Utilisateur::where("token_update_password", $request->password_token)->first();

        if($user) {

            $user->update([
                'password' => Hash::make($request->password),
                'token_update_password' => null,
                'is_valide' => true
            ]);

            Auth::login($user, true);
        }

        return redirect("/login");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register');
    }

    public function inscription(Request $request)
    {
        $request->validate([
            'prenom' => ['required','min:2'],
            'nom' => ['required','min:2'],
            'email' => "required|email|unique:utilisateur,email",
            'telephone' => "required|phone:GN|unique:utilisateur,telephone",
            'adresse' => ['required','min:2'],
        ]);


        $phone = new PhoneNumber($request->telephone, 'GN');

        $request->merge(['telephone' => $phone->formatE164()]);

        $user = new Utilisateur();
        $user->nom = $request->nom;
        $user->uuid = Str::uuid();
        $user->prenom = $request->prenom;
        $user->telephone = $request->telephone;
        $user->adresse = $request->adresse;
        $user->email = $request->email;
        $user->password = Hash::make(1234);
        $user->is_valide = true;
        $user->status_compte = false;
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
                "objet" => "CREATION DE MOT DE PASSE",
                "url" => [
                    'link' => $url,
                    'btn' => "Nouveau mot de passe",
                ],
                "message" => message_email("reunitialise_password"),
            ]
        );

        return redirect('login')->with('message_success', "Votre compte a été créé avec succès. Veillez verifier votre email pour activer votre compte.");

    }

    public function mot_de_passe_oublider() {
        return view('auth.verify');
    }

    public function save_email(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email','regex:/(.+)@(.+)\.(.+)/i']
        ]);

        $use = Utilisateur::where('email',$request->email)->first();

         if (!$use) return back()->withInput()->with("msg", "Adresse email invalide.");
        //dd(Carbon::now()->format("d-m-Y H:i:s"));
        $date1 = Carbon::createFromFormat('d-m-Y H:i:s',Carbon::now()->format("d-m-Y H:i:s"));

        $token_update_password = Str::uuid();

        $user = Utilisateur::find($use->id);
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

       // Mail::to($user->email)->send(new forgetPasswordEmail($data));
        return redirect('/login')->with('messageemail',"Consulter votre G-mail");
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
