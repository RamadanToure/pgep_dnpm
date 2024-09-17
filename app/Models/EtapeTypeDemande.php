<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EtapeTypeDemande
 *
 * @property int $etape_id
 * @property int $type_demande_id
 * @property string|null $uuid
 * @property int $ordre
 * @property int|null $type_paiement_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property TypePaiement|null $typePaiement
 * @property Etape $etape
 * @property TypeDemande $typeDemande
 *
 * @package App\Models
 */
class EtapeTypeDemande extends Model
{
	protected $table = 'etape_type_demande';
    protected $primaryKey = 'uuid';
	public $incrementing = false;
	public static $snakeAttributes = false;

	protected $casts = [
		'etape_id' => 'int',
		'type_demande_id' => 'int',
		'ordre' => 'int',
		'type_paiement_id' => 'int'
	];

	protected $fillable = [
		'uuid',
        'is_mobile_paiement',
		'type_paiement_id',
        'etape_id',
		'type_demande_id',
		'ordre',
        'is_traitement',
        'is_agrement'
	];

	public function typePaiement()
	{
		return $this->belongsTo(TypePaiement::class);
	}

	public function etape()
	{
		return $this->belongsTo(Etape::class);
	}

	public function typeDemande()
	{
		return $this->belongsTo(TypeDemande::class);
	}

    // protected function setKeysForSaveQuery($query)
    // {
    //     $query
    //         ->where('type_demande_id', '=', $this->getAttribute('type_demande_id'))
    //         ->where('type_paiement_id', '=', $this->getAttribute('type_paiement_id'))
    //         ->where('etape_id', '=', $this->getAttribute('etape_id'));
    //     return $query;
    // }
}
