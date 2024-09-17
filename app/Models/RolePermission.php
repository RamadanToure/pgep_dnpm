<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RolePermission
 * 
 * @property int $role_id
 * @property int $permission_id
 * @property string|null $uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Permission $permission
 * @property Role $role
 *
 * @package App\Models
 */
class RolePermission extends Model
{
	use SoftDeletes;
	protected $table = 'role_permission';
	public $incrementing = false;
	public static $snakeAttributes = false;

	protected $casts = [
		'role_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'uuid'
	];

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}

	public function role()
	{
		return $this->belongsTo(Role::class);
	}
}
