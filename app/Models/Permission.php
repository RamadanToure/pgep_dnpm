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
 * Class Permission
 * 
 * @property int $id
 * @property string|null $uuid
 * @property string|null $nom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class Permission extends Model
{
	use SoftDeletes;
	protected $table = 'permission';
	public static $snakeAttributes = false;

	protected $fillable = [
		'uuid',
		'nom'
	];

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_permission')
					->withPivot('uuid', 'deleted_at')
					->withTimestamps();
	}
}
