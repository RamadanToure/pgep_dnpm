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
 * Class TypePaiement
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property int|null $montant
 * @property int|null $type_demande_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property TypeDemande|null $typeDemande
 * @property Collection|EtapeTypeDemande[] $etapeTypeDemandes
 * @property Collection|Paiement[] $paiements
 *
 * @package App\Models
 */
class TypePaiement extends Model
{
	use SoftDeletes;
	protected $table = 'type_paiement';
	public static $snakeAttributes = false;

	protected $casts = [
		'montant' => 'int',
		'type_demande_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'nom',
		'montant',
		'type_demande_id',
        'historique'
	];

	public function typeDemande()
	{
		return $this->belongsTo(TypeDemande::class);
	}

	public function etapeTypeDemandes()
	{
		return $this->hasMany(EtapeTypeDemande::class);
	}

	public function paiements()
	{
		return $this->hasMany(Paiement::class);
	}
}
