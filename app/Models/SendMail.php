<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SendMail
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $message
 * @property bool $is_sent
 * @property string|null $fichier
 * @property string|null $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $utilisateur_id
 * 
 * @property Utilisateur $utilisateur
 *
 * @package App\Models
 */
class SendMail extends Model
{
	protected $table = 'send_mail';
	public static $snakeAttributes = false;

	protected $casts = [
		'is_sent' => 'bool',
		'utilisateur_id' => 'int'
	];

	protected $fillable = [
		'title',
		'message',
		'is_sent',
		'fichier',
		'url',
		'utilisateur_id'
	];

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class);
	}
}
