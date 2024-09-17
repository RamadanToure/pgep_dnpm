<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EtapeTypeDemande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\{Document, TypeDocument, Service, Demande, ServiceDemandeService, DemandeEtape};
use Illuminate\Support\Str;
use App\Services\GestionnaireEmail;

/**
 * Class Demande
 *
 * @property int $id
 * @property string|null $uuid
 * @property int $etape_id
 * @property int $type_demande_id
 * @property int|null $utilisateur_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Etape $etape
 * @property TypeDemande $typeDemande
 * @property Utilisateur|null $utilisateur
 * @property Collection|Document[] $documents
 * @property Collection|Paiement[] $paiements
 *
 * @package App\Models
 */
class Demande extends Model
{
	use SoftDeletes;
	protected $table = 'demande';
	public static $snakeAttributes = false;

	protected $casts = [
		'etape_id' => 'int',
		'type_demande_id' => 'int',
		'utilisateur_id' => 'int',
        'status' => 'int'
	];

	protected $fillable = [
		'uuid',
        'code_sms',
        'numero_demande',
        'notif_token',
		'etape_id',
        'status',
		'type_demande_id',
		'utilisateur_id',
        'soumis',
        'niveau',
        'is_approuved',
	];

    function changeStatus($status){
        $this->update(['status' => $status]);
    }

    function changeStep($step = null) {


        if($step) {
            $etape = EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)
                ->whereOrdre($step)
                ->first();

            if($etape) $this->update(['etape_id' => $etape->etape_id]);

            return;
        }

        $etape = EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)->whereEtapeId($this->etape_id)->first();

        if($etape) {

            $etape = EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)
                ->whereOrdre($etape->ordre+1)
                ->first();

            if($etape) $this->update(['etape_id' => $etape->etape_id]);
        }

    }

    // public function etapes()
	// {
	// 	return EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)->orderBy('ordre');
	// }

    function initStep() {

        $steps = EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)->orderBy('ordre')->get();

        foreach ($steps as $key => $step) {

            DemandeEtape::create([
                'uuid' => Str::uuid(),
                'ordre' => $step->ordre,
                'etape_id' => $step->etape->id,
                'demande_id' => $this->id,
                'is_traitement' => $step->is_traitement,
                'is_agrement' => $step->is_agrement,
                'is_mobile_paiement' => $step->is_mobile_paiement,
		        'type_paiement_id' => $step->type_paiement_id,
            ]);
        }
    }

    function resetDemande() {

        // $firstStep = EtapeTypeDemande::whereTypeDemandeId($this->type_demande_id)->whereOrdre(1)->first();

        // if($firstStep) {

        //     DemandeEtape::whereEtapeId($this->etape_id)->whereDemandeId($this->id)->update([
        //         'status' => true
        //     ]);
        // }
    }

    function validStep() {
        DemandeEtape::whereEtapeId($this->etape_id)->whereDemandeId($this->id)->update([
            'status' => true
        ]);
    }

    function setCurrentMethode($methode, $etape) {
        //methode_paiement
        DemandeEtape::whereEtapeId($etape->id)->whereDemandeId($this->id)->update([
            'methode_paiement' => $methode
        ]);
    }

    public function etapes()
    {
        return $this->belongsToMany(Etape::class, 'demande_etape', 'demande_id', 'etape_id')
            ->withPivot('uuid', 'ordre', 'status', 'is_mobile_paiement', 'type_paiement_id', 'recu_paiement',
            'recu_paiement_preview', 'is_traitement', 'is_agrement', 'methode_paiement');
    }

    function isCurrentStep($etape) {
        return $this->etape_id == $etape->id;
    }

	public function etape()
	{
		return $this->belongsTo(Etape::class);
	}

	public function typeDemande()
	{
		return $this->belongsTo(TypeDemande::class);
	}

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class);
	}

	public function documents()
	{
		return $this->hasMany(Document::class);
	}

    public function transmissions()
	{
		return $this->hasMany(ServiceDemandeService::class);
	}

    function getTypeDocuments($etape) {

        return TypeDocument::whereIn('id', function ($query) use ($etape) {
            $query->from('type_demande_type_document_etape')
            ->whereTypeDemandeId($this->type_demande_id)
            ->whereEtapeId($etape->id)->select('type_document_id')->get();
        });
    }

    public function getDocPreviewImage($etape, $type) {

        $doc = $this->documents()->where('type_document_id', $type->id)->where('etape_id', $etape->id);

        if($doc->exists()) return asset(Storage::url($doc->first()->preview));

        return ;
    }

	public function paiements()
	{
		return $this->hasMany(Paiement::class);
	}

    function getPaiementByEtape($etape) {
        return $this->paiements()->whereEtapeId($etape->id)->first();
    }

    public function services()
	{
		return $this->belongsToMany(Service::class, 'service_demande_service', 'demande_id', 'service_expediteur_id')
					->withPivot('id', 'uuid', 'file', 'note', 'status', 'service_destinataire_id')
					->withTimestamps();
	}

    function getServices() {

        return Service::where(function ($query) {
            $query->whereIn("id", function ($query) {
                $query->from("service_demande_service")->whereDemandeId($this->id)
                ->select("service_expediteur_id")->get();
            })->orWhereIn('id', function ($query) {
                $query->from("service_demande_service")->whereDemandeId($this->id)
                ->select("service_destinataire_id")->get();
            })->orWhereIn('type_service_id', function ($query) {
                $query->from("type_service")->whereNom("Service central")->select("id")->get();
            });
        });
        //->where('utilisateur_id', '<>', auth()->user()->id);
    }

    function transmettre($from, $to, $status = false) {

        ServiceDemandeService::whereDemandeId($this->id)->update([
            'status' => true
        ]);

        $transmission =  ServiceDemandeService::create([
            'status' => $status,
            'date_transmission' => now(),
            'uuid' => Str::uuid(),
            'demande_id' => $this->id,
            'service_expediteur_id' => $from,
            'service_destinataire_id' => $to
        ]);

        //Envoi du mail au demandeur
        if($from) {
            GestionnaireEmail::envoyerEmailTransmission(
                $this->utilisateur,
                $from,
                $to
            );
        }
        $to = Service::find($to);

        if($transmission->serviceDestinataire AND $transmission->serviceDestinataire->utilisateur) {

            GestionnaireEmail::envoyerEmailTransmissionService(
                $transmission->serviceDestinataire->utilisateur,
                $this,
                $transmission
            );
        }

        return $transmission;
    }

    function canTransfert() {
        return ServiceDemandeService::whereStatus(false)->whereDemandeId($this->id)->whereIn('service_destinataire_id', function ($query) {
            $query->from("service")->whereUtilisateurId(\auth()->user()->id)->select("id")->get();
        })->exists();
    }

    function servicesTraitant() {
        return Service::whereIn('id', function ($query) {
            $query->from("service_demande_service")->whereDemandeId($this->id)->select("service_expediteur_id")->get();
        })->get();
    }
}
