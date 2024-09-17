<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Payment\OrangeMoney;
use App\Models\{Paiement, Demande, TypePaiement, Etape};

class ProcessusPaiementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $demande;
    public $type;
    public $etape;
    private $response;

    /**
     * Create a new job instance.
     */
    public function __construct(Demande $demande, TypePaiement $type, Etape $etape)
    {
        $this->demande = $demande;
        $this->etape = $etape;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $om = new OrangeMoney();
        $om->makePayment($this->demande, $this->type, $this->etape);
        $this->response = $om;
        return $om;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
