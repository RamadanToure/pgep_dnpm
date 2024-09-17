<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GestionnaireEmail;

class EnvoiEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $destinataire;
    protected $sujet;
    protected $contenu;

    /**
     * Create a new job instance.
     */
    public function __construct($destinataire, $sujet, $contenu)
    {
        $this->destinataire = $destinataire;
        $this->sujet = $sujet;
        $this->contenu = $contenu;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        GestionnaireEmail::envoyerEmail($this->destinataire, $this->sujet, $this->contenu);
    }
}
