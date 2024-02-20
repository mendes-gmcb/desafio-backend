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

        $payer = User::where('id', $request->payer)->firstOrFail();
        $payee = User::where('id', $request->payee)->firstOrFail();
        // return response()->json([$payer, $payee]);

        // Verifica se o usuário que está enviando a transferência é um usuário comum
        // Verifica se o usuário que está enviando a transferência tem saldo suficiente
        // Consulta o serviço autorizador externo
        // Verifica se a consulta foi bem-sucedida
        // Verifica se a transferência foi autorizada
            // Realiza a transferência de dinheiro
            $transaction = Transaction::create([
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => $request->value,
                'type' => $request->type,
                'description' => $request->description,
            ]);
            return response()->json($transaction, 201);
    }
}