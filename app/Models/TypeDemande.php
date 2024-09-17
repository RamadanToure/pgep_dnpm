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

/**
 * Class TypeDemande
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property int $service_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Service $service
 * @property Collection|Demande[] $demandes
 * @property Collection|Etape[] $etapes
 * @property Collection|TypeDocument[] $typeDocuments
 * @property Collection|TypePaiement[] $typePaiements
 *
 * @package App\Models
 */
class TypeDemande extends Model
{
	use SoftDeletes;
	protected $table = 'type_demande';
	public static $snakeAttributes = false;

	protected $casts = [
		'service_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'nom',
		'service_id'
	];

	public function service()
	{
		return $this->belongsTo(Service::class);
	}

	public function demandes()
	{
		return $this->hasMany(Demande::class);
	}

    public function getFirstStep() {
        return EtapeTypeDemande::whereTypeDemandeId($this->id)->whereOrdre(1)->first();
    }

    public function etapes()
	{
		return $this->belongsToMany(Etape::class)
					->withPivot('uuid', 'ordre', 'type_paiement_id')
					->withTimestamps();
	}

	// public function etapes()
	// {
	// 	return $this->belongsToMany(Etape::class, 'type_demande_type_document_etape')
	// 				->withPivot('type_document_id', 'deleted_at')
	// 				->withTimestamps();
	// }

	public function typeDocuments()
	{
		return $this->belongsToMany(TypeDocument::class, 'type_demande_type_document_etape')
					->withPivot('etape_id')
					->withTimestamps();
	}

	public function typePaiements()
	{
		return $this->hasMany(TypePaiement::class);
	}
}
