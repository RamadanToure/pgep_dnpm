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
 * Class Etape
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Collection|Demande[] $demandes
 * @property Collection|Document[] $documents
 * @property Collection|TypeDemande[] $typeDemandes
 * @property Collection|Paiement[] $paiements
 * @property Collection|TypeDocument[] $typeDocuments
 *
 * @package App\Models
 */
class Etape extends Model
{
	use SoftDeletes;
	protected $table = 'etape';
	public static $snakeAttributes = false;

	protected $fillable = [
		'uuid',
		'nom'
	];

	public function demandes()
	{
		return $this->hasMany(Demande::class);
	}

	public function documents()
	{
		return $this->hasMany(Document::class);
	}

	public function typeDemandes()
	{
		return $this->belongsToMany(TypeDemande::class, 'type_demande_type_document_etape')
					->withPivot('type_document_id')
					->withTimestamps();
	}

	public function paiements()
	{
		return $this->hasMany(Paiement::class);
	}

	public function typeDocuments()
	{
		return $this->belongsToMany(TypeDocument::class, 'type_demande_type_document_etape')
					->withPivot('type_demande_id')
					->withTimestamps();
	}
}
