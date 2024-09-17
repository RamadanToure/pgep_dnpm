<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Paiement, Demande, TypePaiement, Etape};
use Illuminate\Support\Str;
use App\Services\GestionnaireEmail;

class PaiementController extends Controller
{
    function notifyOrangeMoney(Request $request, $type, $demande, $etape)
    {
        if($request->has('status') AND $request->status == "SUCCESS") {

            $type = TypePaiement::whereUuid($type)->first();
            $demande = Demande::whereUuid($demande)->first();
            $etape = Etape::whereUuid($etape)->first();

            if($type AND $demande->notif_token == $request->notif_token) {

                $paiement = Paiement::create([
                    'status' => 2,
                    'uuid' => Str::uuid(),
                    'montant' => $type->montant,
                    'date_paiement' => now(),
                    'demande_id' => $demande->id,
                    'type_paiement_id' => $type->id,
                    'etape_id' => $etape->id
                ]);

                $demande->validStep();
                $demande->changeStep();

                $demande->update(['notif_token' => null]);

                //Envoi du mail
                GestionnaireEmail::envoyerEmailConfirmationPaiement(
                    $demande->utilisateur,
                    $paiement,
                    false
                );
            }
        }
    }
}
