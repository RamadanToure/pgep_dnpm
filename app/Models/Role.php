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
 * Class Role
 * 
 * @property int $id
 * @property string $nom
 * @property string $description
 * @property string|null $uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Permission[] $permissions
 * @property Collection|Utilisateur[] $utilisateurs
 *
 * @package App\Models
 */
class Role extends Model
{
	use SoftDeletes;
	protected $table = 'role';
	public static $snakeAttributes = false;

	protected $fillable = [
		'nom',
		'description',
		'uuid'
	];

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_permission')
					->withPivot('uuid', 'deleted_at')
					->withTimestamps();
	}

	public function utilisateurs()
	{
		return $this->hasMany(Utilisateur::class);
	}
}
