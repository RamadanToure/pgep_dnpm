<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Service
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property int $type_service_id
 * @property int $utilisateur_id
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property TypeService $typeService
 * @property Utilisateur $utilisateur
 * @property Service|null $service
 * @property Collection|Service[] $services
 * @property Collection|Demande[] $demandes
 * @property Collection|TypeDemande[] $typeDemandes
 *
 * @package App\Models
 */
class Service extends Model
{
	use SoftDeletes;
	protected $table = 'service';
	public static $snakeAttributes = false;

	protected $casts = [
		'type_service_id' => 'int',
		'utilisateur_id' => 'int',
		'parent_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'nom',
        'sigle',
		'type_service_id',
		'utilisateur_id',
		'parent_id',
        'is_central'
	];

	public function typeService()
	{
		return $this->belongsTo(TypeService::class);
	}

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class);
	}

	public function service()
	{
		return $this->belongsTo(Service::class, 'parent_id');
	}

	public function services()
	{
		return $this->hasMany(Service::class, 'parent_id');
	}

	public function demandes()
	{
		return $this->belongsToMany(Demande::class, 'service_demande_service', 'service_expediteur_id')
					->withPivot('id', 'uuid', 'file', 'note', 'status', 'service_destinataire_id')
					->withTimestamps();
	}

	public function typeDemandes()
	{
		return $this->hasMany(TypeDemande::class);
	}

    function getLastMessage($demande) {
        $message = ServiceDemandeService::whereDemandeId($demande->id)
        ->where(function ($query) {
            $query->where("service_destinataire_id", $this->id)->orWhere("service_expediteur_id", $this->id);
        })->orderBy('date_transmission', "DESC")->first();

        return $message->note ?? "";
    }

    function getMessages($demande) {
        return ServiceDemandeService::whereDemandeId($demande->id)
        ->where(function ($query) {
            $query->where("service_destinataire_id", $this->id)->orWhere("service_expediteur_id", $this->id);
        })->whereNotNull("service_expediteur_id")->orderBy('date_transmission', "DESC")->get();
    }
}
