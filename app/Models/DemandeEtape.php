<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DemandeEtape
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $recu_paiement
 * @property string|null $recu_paiement_preview
 * @property bool $status
 * @property int|null $ordre
 * @property bool $is_mobile_paiement
 * @property int|null $type_paiement_id
 * @property int $etape_id
 * @property int|null $demande_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Demande|null $demande
 * @property Etape $etape
 * @property TypePaiement|null $typePaiement
 *
 * @package App\Models
 */
class DemandeEtape extends Model
{
	protected $table = 'demande_etape';
	public static $snakeAttributes = false;

	protected $casts = [
		'status' => 'bool',
		'ordre' => 'int',
		'is_mobile_paiement' => 'bool',
		'type_paiement_id' => 'int',
		'etape_id' => 'int',
		'demande_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'recu_paiement',
		'recu_paiement_preview',
		'status',
		'ordre',
		'is_mobile_paiement',
		'type_paiement_id',
		'etape_id',
		'demande_id',
        'is_traitement',
        'is_agrement'
	];

	public function demande()
	{
		return $this->belongsTo(Demande::class);
	}

	public function etape()
	{
		return $this->belongsTo(Etape::class);
	}

	public function typePaiement()
	{
		return $this->belongsTo(TypePaiement::class);
	}
}
