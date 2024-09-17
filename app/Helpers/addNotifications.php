<?php

use App\Models\Utilisateur;
use App\Models\{Notification, SentMail,SendMail};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\{sendNotificationsToPromoteur};
use Illuminate\Support\Facades\Auth;
use App\Models\PendingEmail;
use App\Mail\AllUserMailEmail;


// // notification
function notification(Utilisateur $recever, $object, $message, $ong = null, $rdv = null ){
    $sender = Auth::user();

    if(!$sender) $sender = $recever;

    $notification = new Notification;

    $notification->objet = $object;

    $notification->sender_id = $sender->id;
    $notification->recever_id = $recever->id;
    if($ong) {
        $notification->ong_id = $ong->id;
        $notification->contenu = $message;
    } else {
        $notification->contenu = $message;
    }
    // $notification->isUpdate = 1;
    $notification->save();
}


function compteurNotifications(){
    $notificationNombre = DB::table('notification')
                ->where('recever_id',auth()->user()->id)
                ->where('isUpdate', 0)
                ->get();
        $valeur = count($notificationNombre);
    return $valeur;
}

function notificationPreview(){
        $notification= DB::table('notification')
                ->where('recever_id',auth()->user()->id)
                ->where('isUpdate', 0)
                ->orderBy('notification.created_at', 'desc')
                ->paginate(3);
    return $notification;
}

function filtre_message($message)
{
    if(is_array($message)) {
        $collect = collect($message)->filter(function ($text, $key) {
            $rexg = [
                "#^Bonjour Mme/Mr#",
                "#^Mme#",
                "#Veuillez vous connecter#"
            ];
            return (!preg_match($rexg[0], $text) AND !preg_match($rexg[1], $text) AND !preg_match($rexg[2], $text));
        });

        return $collect->join(" ");
    }

    return $message;
}


function send_notification(Utilisateur $receiver, $object, $message, $ong = null, $url = null, $onlyMail = 0,$type_notification=null){

    $sender = Auth::user();

    if(!$sender) $sender = $receiver;

    $notification = new Notification;

    $notification->objet = $object;

    $notification->sender_id = $sender->id;
    $notification->recever_id = $receiver->id;
    if($ong) {
        $notification->ong_id = $ong->id;
        $notification->contenu = filtre_message($message);
    } else {
        $notification->contenu = filtre_message($message);
    }
    $notification->type_notification = $type_notification;
    // $notification->isUpdate = 1;

   /*  if($onlyMail == 0 ){

    } */
    $notification->save();
    $init_msg = $message;

    $send_mail = new SendMail();
    if(is_array($message)) {
        $message = collect($message)->join("|");
    }

    $send_mail->title = $object;
    $send_mail->utilisateur_id = $receiver->id;
    $send_mail->message = $message;
    $send_mail->is_sent = false;
    $send_mail->url = $url;
    $send_mail->ong_id = $ong ? $ong->id : null;

    $send_mail->save();

    // try {
    //     Mail::to($receiver->email)->send(new sendNotificationsToPromoteur($receiver, $object, $init_msg, $ong, $url));
    // } catch (\Symfony\Component\Mailer\Exception\TransportException $th) {
    //     //Test
    // }
}

use Illuminate\Support\Str;
function trunWordNotif($content = "") {
    return Str::words($content, 10, ' ...');
}

function saveMailToDatabase($to, $mailable){

    $email = new PendingEmail([
        'to' => $to->email,
        'mailable' => json_encode($mailable)
    ]);
    $email->save();
    
    // $notification = Notification::create([
    //     'objet' => $mailable['objet'],
	// 	'contenu' => filtre_message($mailable['message']),
	// 	'sender_id' => $to->id,
	// 	'recever_id' => $to->id
    // ]);

    // if(isset($mailable['ong'])) $notification->update(['ong_id' => $mailable['ong']]);
}
