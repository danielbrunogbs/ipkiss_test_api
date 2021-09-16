<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Exceptions\RequestException;
use App\Models\AccountHistory;

trait AccountTrait
{
	public function deposit(Request $request)
	{
		$account = $this->find($request->destination);

		if(!$account)
		{
			$account = $this->create([
				'id' => (int) $request->destination,
				'balance' => (float) $request->amount
			]);
		}
		else
		{
			$account->balance += (float) $request->amount;
			$account->save();
		}

		AccountHistory::create([
			'account_id' => $account->id,
			'operation' => 'deposit',
			'request' => json_encode($request->all())
		]);

		return [
			[
				'destination' => [
					'id' => (string) $account->id,
					'balance' => $account->balance
				]
			],
			201
		];
	}

	public function withdraw(Request $request)
	{
		$account = $this->find($request->origin);

		if(!$account)
			throw new RequestException('Conta não localizada!', 404);

		$account->balance -= $request->amount;

		$account->save();

		AccountHistory::create([
			'account_id' => $account->id,
			'operation' => 'withdraw',
			'request' => json_encode($request->all())
		]);

		return [
			[
					'origin' => [
						'id' => (string) $account->id,
						'balance' => $account->balance
				]
			],
			201
		];
	}

	public function transfer(Request $request)
	{
		$accountOrigin = $this->find($request->origin);

		if(!$accountOrigin)
			throw new RequestException('Conta de origem não localizada!', 404);

		$accountDestination = $this->find($request->destination);

		if(!$accountDestination)
			throw new RequestException('Conta de destino não localizada!', 404);

		$accountOrigin->balance -= $request->amount;
		$accountDestination->balance += $request->amount;

		$accountOrigin->save();
		$accountDestination->save();

		AccountHistory::create([
			'account_id' => $accountOrigin->id,
			'operation' => 'transfer',
			'request' => json_encode($request->all())
		]);

		return [
			[
				'origin' => [
					'id' => (string) $accountOrigin->id,
					'balance' => $accountOrigin->balance
				],
				'destination' => [
					'id' => (string) $accountDestination->id,
					'balance' => $accountDestination->balance
				]
			],
			201
		];
	}
}