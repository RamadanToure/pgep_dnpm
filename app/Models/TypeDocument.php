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
 * Class TypeDocument
 *
 * @property int $id
 * @property string|null $uuid
 * @property string $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Collection|Document[] $documents
 * @property Collection|TypeDemande[] $typeDemandes
 *
 * @package App\Models
 */
class TypeDocument extends Model
{
	use SoftDeletes;
	protected $table = 'type_document';
	public static $snakeAttributes = false;

	protected $fillable = [
		'uuid',
		'nom'
	];

	public function documents()
	{
		return $this->hasMany(Document::class);
	}

    function getDocument($demande) {
        return $this->documents()->whereDemandeId($demande->id)->first();
    }

	public function typeDemandes()
	{
		return $this->belongsToMany(TypeDemande::class)
					->withPivot('deleted_at')
					->withTimestamps();
	}
}
