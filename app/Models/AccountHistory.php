<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountHistory extends Model
{
	protected $fillable = ['account_id', 'operation', 'request'];

	public function account()
	{
		return $this->hasOne(Account::class, 'id', 'account_id');
	}
}