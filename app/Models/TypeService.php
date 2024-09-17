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
 * Class TypeService
 * 
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Service[] $services
 *
 * @package App\Models
 */
class TypeService extends Model
{
	use SoftDeletes;
	protected $table = 'type_service';
	public static $snakeAttributes = false;

	protected $fillable = [
		'uuid',
		'nom'
	];

	public function services()
	{
		return $this->hasMany(Service::class);
	}
}
