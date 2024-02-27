<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactMoneyRequest;
use App\Jobs\SendNotificationJob;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionAuthorizationService;
use DB;

enum Type: string {
    case Debit = "d";
    case Credit = "c";
    case Pix = "p";
}

class TransactionController extends Controller
{
    protected TransactionAuthorizationService $authorizationService;
    public function __construct() {
        $this->authorizationService = new TransactionAuthorizationService();
    }

    public function transactMoney(TransactMoneyRequest $request)
    {
        try {
            $payer = $this->getUserById($request->payer);
            $payee = $this->getUserById($request->payee);

            $this->checkPayerAndBalance($payer, $request->value);

            $this->authorizationService->authorize('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc');

            $transaction = $this->performTransaction($payer, $payee, $request->value, $request->type, $request->description);

            SendNotificationJob::dispatch($payee, $transaction);

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    protected function getUserById($userId)
    {
        return User::findOrFail($userId);
    }

    protected function performTransaction(User $payer, User $payee, int $value, string $type, string $description)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]);

            $payer->decrement('balance', $value);
            $payee->increment('balance', $value);

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Ocorreu um erro ao processar a transação.', 500);
        }
    }

    protected function checkPayerAndBalance(User $payer, $value)
    {
        // Verifica se o usuário que está enviando a transferência é um usuário comum
        if ($payer->type === 'store') {
            // Lança uma exceção ou retorna uma mensagem de erro
            throw new \Exception("Lojistas não podem enviar dinheiro para outros usuários.", 403);
        }

        // Verifica se o usuário que está enviando a transferência tem saldo suficiente
        if ($payer->balance < $value) {
            // Retorna uma mensagem de erro
            throw new \Exception("Você não tem saldo suficiente para realizar essa transferência.", 400);
        }
    }
}