<?php

namespace App\Http\Controllers;

use App\Exceptions\RequestException;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'account_id' => 'required|integer'
        ]);

        $account = Account::find($request->query('account_id'));

        if(!$account)
            throw new RequestException('Conta não localizada!', 404);

        return response()->json($account->balance, 200);
    }

    public function reset()
    {
        $accounts = Account::select('id')->get();

        Account::whereIn('id', $accounts)->delete();

        Account::create([
            'id' => 300,
            'balance' => 0
        ]);

        return response()->json(['message' => 'Aplicação resetada!'], 200);
    }

    public function event(Request $request)
    {
        DB::beginTransaction();
        
        try
        {
            $this->validate($request, [
                'type' => 'required',
                'amount' => 'required'
            ]);

            $type = $request->type;

            if(!in_array($type, ['deposit', 'withdraw', 'transfer']))
                throw new RequestException('Método não permitido!');

            $account = new Account();

            if(!method_exists($account, $type))
                throw new RequestException('Evento não localizado!', 404);

            $operation = $account->$type($request);

            DB::commit();

            return response()->json($operation, 200);
        }
        catch(\Exception $e)
        {
            DB::rollback();

            throw $e;
        }
    }
}