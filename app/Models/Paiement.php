<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Paiement
 *
 * @property int $id
 * @property string|null $uuid
 * @property int|null $montant
 * @property Carbon|null $date_paiement
 * @property int|null $demande_id
 * @property int|null $type_paiement_id
 * @property int|null $etape_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Demande|null $demande
 * @property Etape|null $etape
 * @property TypePaiement|null $typePaiement
 *
 * @package App\Models
 */
class Paiement extends Model
{
	use SoftDeletes;
	protected $table = 'paiement';
	public static $snakeAttributes = false;

	protected $casts = [
		'montant' => 'int',
		'date_paiement' => 'datetime',
		'demande_id' => 'int',
		'type_paiement_id' => 'int',
		'etape_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'montant',
		'date_paiement',
		'demande_id',
		'type_paiement_id',
		'etape_id',
        'status',
        'note',
        'date_status'
	];

    function getStatus() {

        $prefix_1 = $this->demande->utilisateur->is(auth()->user()) ? "Votre demande":"La demande";
        $prefix_2 = $this->demande->utilisateur->is(auth()->user()) ? "Votre paiement":"Le Paiement";

        switch ($this->status) {
            case 0:
                return [
                    "text" => "$prefix_1 de paiement est en attente de vérification.",
                    "class" => "text-warning"
                ];
            case 1:
                return [
                    "text" => "$prefix_1 de paiement est en cours de vérification.",
                    "class" => "text-info"
                ];
            case 2:
                return [
                    "text" => "$prefix_2 a été validé avec succès.",
                    "class" => "text-success"
                ];
            case 3:
                return [
                    "text" => "$prefix_2 a été refusé.",
                    "class" => "text-danger"
                ];
            default:
                return [
                    "text" => "Votre statut est inconnu.",
                    "class" => "text-muted"
                ];
        }
    }

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
