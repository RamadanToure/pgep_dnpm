<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Document
 *
 * @property int $id
 * @property int $type_document_id
 * @property string|null $file
 * @property string|null $preview
 * @property int|null $demande_id
 * @property int|null $etape_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Demande|null $demande
 * @property Etape|null $etape
 * @property TypeDocument $typeDocument
 *
 * @package App\Models
 */
class Document extends Model
{
	use SoftDeletes;
	protected $table = 'document';
	public static $snakeAttributes = false;

	protected $casts = [
		'type_document_id' => 'int',
		'demande_id' => 'int',
		'etape_id' => 'int',
        'status' => 'int'
	];

	protected $fillable = [
		'type_document_id',
		'file',
		'preview',
		'demande_id',
		'etape_id',
        'status',
        'motif_rejet'
	];

    function getStatus() {
        return [
            0 => 'En attente de traitement',
            1 => 'Validée',
            2 => 'Rejetée'
        ][$this->status];
    }

	public function demande()
	{
		return $this->belongsTo(Demande::class);
	}

	public function etape()
	{
		return $this->belongsTo(Etape::class);
	}

	public function typeDocument()
	{
		return $this->belongsTo(TypeDocument::class);
	}
}
