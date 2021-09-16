<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AccountTrait;

class Account extends Model
{
	use AccountTrait;

	protected $fillable = ['id', 'balance'];

	public function histories()
	{
		return $this->hasMany(AccountHistory::class, 'account_id', 'id');
	}
}