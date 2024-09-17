<?php

function message_email($attribut_message, $ong = null, $motif = null) {

    $nom = $ong ? $ong->nom : "" ;
    $motif = $motif ? $motif : "" ;

    $message_emails = [

        "inscription" => ["Bonjour Mme/Mr,","Vous avez reçu une demande de création de compte.","Veuillez vous connecter sur la plateforme pour examiner la demande."],

        "demande_creation_compte" => ["Votre demande de création de compte a été enregistrée avec succès.","Elle est en cours de verification.","Veuillez patienter, vous recevrez une confirmation dans les plus brefs délais."],

        "activation_compte" => ["Votre compte a été validé avec succès.","Rendez-vous dans votre espace pour finaliser votre demande.","À bientôt."],

        "desactivation_compte" => ["Votre compte a été desactivé!.","Veillez contacter notre administrateur.","À bientôt."],

        "mot_de_passe_oublier" => ["Pour mettre à jour votre mot de passe", "Veuillez cliquer le lien"],

        "alert_nouveau_ong" => ["Vous avez une nouvelle demande d'activation de compte"],

        "nouvelle_ong" => ["Vous avez une nouvelle demande d'agrément à consulter"],

        "etude_documentation_terminer" => ["Veiller consulter votre compte, votre pour continuer le processus"],

        "agreement_complete" => ["Veiller consulter votre compte","Merci de votre bonne collaborations"],

        "reunitialise_password" => ["Votre mot de passe a été reunitialisé","Veillez entrer un nouveau mot de passe"],

        "creation_compte_utilisateur" => ["Votre compte a été créé avec succès.","Veillez verifier votre email pour activer votre compte."],
    ];

    return $message_emails[$attribut_message];
}
