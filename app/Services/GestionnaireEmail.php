<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Content;
use App\Mail\SendMail;
use App\Models\{Service};
use App\Models\SendMail as Notif;
use App\Jobs\EnvoiEmailJob;

class GestionnaireEmail
{
    public static function envoyerEmail($destinataire, $sujet, $contenu)
    {
        //Mail::to($destinataire)->send(new SendMail($destinataire, $sujet, $contenu));

        Notif::create([
            'title' => $sujet,
            'message' => serialize($contenu),
            'utilisateur_id' => $destinataire->id
        ]);
    }

    public static function envoyerEmailRejetDocuments($destinataire, $documents)
    {
        $sujet = 'Documents rejetés';
        $contenu = new Content(
            markdown: 'emails.rejet_documents',
            with: [
                'utilisateur' => $destinataire,
                'documents' => $documents
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

    public static function envoyerEmailValidationDocuments($destinataire, $demande)
    {
        $sujet = 'Confirmation de validation des documents';
        $contenu = new Content(
            markdown: 'emails.valider_documents',
            with: [
                'utilisateur' => $destinataire,
                'demande' => $demande
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

    public static function envoyerEmailConfirmationPaiement($destinataire, $paiement, $virement = true)
    {
        $sujet = "Confirmation de réception et validation de votre paiement";
        $contenu = new Content(
            markdown: 'emails.confirmation_paiement',
            with: [
                'utilisateur' => $destinataire,
                'paiement' => $paiement,
                'virement' => $virement
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

    public static function envoyerEmailTransmission($destinataire, $from, $to)
    {
        $sujet = 'Transmission réussie de votre dossier';
        $contenu = new Content(
            markdown: 'emails.transmission',
            with: [
                'utilisateur' => $destinataire,
                'from' => Service::find($from),
                'to' => Service::find($to),
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

    public static function envoyerEmailTransmissionService($destinataire, $demande, $transmission)
    {
        $sujet = "Nouvelle demande d'agrément à examiner";
        $contenu = new Content(
            markdown: 'emails.transmission_service',
            with: [
                'utilisateur' => $destinataire,
                'demande' => $demande,
                'transmission' => $transmission
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

    public static function envoyerEmailNouvelleDemande($destinataire, $demande)
    {
        $sujet = "Nouvelle demande d'agrément à traiter";
        $contenu = new Content(
            markdown: 'emails.nouvelle_demande',
            with: [
                'utilisateur' => $destinataire,
                'demande' => $demande
            ],
        );

        self::envoyerEmail($destinataire, $sujet, $contenu);
    }

}
