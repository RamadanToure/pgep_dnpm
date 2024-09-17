<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class sendNotificationsToPromoteur extends Mailable
{
    use Queueable, SerializesModels;

    public $receiver, $object, $init_msg, $ong, $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($receiver, $object, $init_msg, $ong, $url)
    {
        $this->receiver = $receiver;
        $this->object = $object;
        $this->init_msg = $init_msg;
        $this->ong = $ong;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MINISTÈRE DE L’ADMINISTRATION DU TERRITOIRE ET DE LA DÉCENTRALISATION',
        );
    }
    public function content(): Content
    {
        $role = strtolower($this->receiver->role->nom);
        $build =  "";
        if ($role == "ong") {
            $build = 'mails.contenusMail';
        } else if ($role == "admin"){
            $build = 'mails.MailForAdminFromOng';
        } else if ($role == "senasol") {
            $build = 'mails.contenusMail';
        }
        $content = new Content(
            markdown: $build,
            with: [
                'receiver' => $this->receiver,
                'object' => $this->object,
                'init_msg' => $this->init_msg,
                'ong' => $this->ong,
                'url' => $this->url
            ]
        );
        return $content;
    }

}
