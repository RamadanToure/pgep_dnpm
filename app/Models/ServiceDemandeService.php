<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ServiceDemandeService
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $file
 * @property string|null $note
 * @property bool $status
 * @property int|null $demande_id
 * @property int|null $service_expediteur_id
 * @property int|null $service_destinataire_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Demande|null $demande
 * @property Service|null $service
 *
 * @package App\Models
 */
class ServiceDemandeService extends Model
{
	protected $table = 'service_demande_service';
	public static $snakeAttributes = false;

	protected $casts = [
		'status' => 'bool',
		'demande_id' => 'int',
		'service_expediteur_id' => 'int',
		'service_destinataire_id' => 'int'
	];

	protected $fillable = [
		'uuid',
        'date_transmission',
		'file',
		'note',
		'status',
		'demande_id',
		'service_expediteur_id',
		'service_destinataire_id'
	];

	public function demande()
	{
		return $this->belongsTo(Demande::class);
	}

	public function serviceExpediteur()
	{
		return $this->belongsTo(Service::class, 'service_expediteur_id');
	}

    public function serviceDestinataire()
	{
		return $this->belongsTo(Service::class, 'service_destinataire_id');
	}
}
