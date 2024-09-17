<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingEmail extends Model
{
    use HasFactory;
    
    protected $table = 'pending_emails';
	public static $snakeAttributes = false;

	protected $casts = [
		'is_sent' => 'bool'
	];

	protected $fillable = [
		'to',
		'subject',
		'mailable',
		'mailable_data',
		'is_sent'
	];
}
