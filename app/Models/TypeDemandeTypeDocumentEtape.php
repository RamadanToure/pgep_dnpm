<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeDemandeTypeDocumentEtape
 *
 * @property int $etape_id
 * @property int $type_demande_id
 * @property int $type_document_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property TypeDemande $typeDemande
 * @property Etape $etape
 * @property TypeDocument $typeDocument
 *
 * @package App\Models
 */
class TypeDemandeTypeDocumentEtape extends Model
{
	protected $table = 'type_demande_type_document_etape';
	public $incrementing = false;
	public static $snakeAttributes = false;
    protected $primaryKey = false;

	protected $casts = [
		'etape_id' => 'int',
		'type_demande_id' => 'int',
		'type_document_id' => 'int'
	];

    protected $fillable = [
		'etape_id',
		'type_demande_id',
		'type_document_id'
	];

	public function typeDemande()
	{
		return $this->belongsTo(TypeDemande::class);
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
