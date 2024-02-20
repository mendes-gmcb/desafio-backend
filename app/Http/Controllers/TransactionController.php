<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function transactMoney(Request $request)
    {
        // Valida os dados da requisição
        $request->validate([
            'payer' => ['required', 'uuid', 'exists:users,id'],
            'payee' => ['required', 'uuid', 'exists:users,id'],
            'value' => ['required', 'int', 'min:1'],
            'type' => ['required', 'in:c,d,p'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        // return response(200);
    }
}