<?php

namespace Tests\Feature;

use App\Models\User;
use Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_send_money_to_another_user()
    {
        // Cria dois usuários
        $payer = User::factory()->create(['balance' => 100]);
        $payee = User::factory()->create(['balance' => 0]);

        $transaction = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 50,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para usuário comum',
        ];

        // Tenta realizar uma transferência
        $response = $this->actingAs($payer)->postJson('/transactions', $transaction);

        $response->assertStatus(201);
        $response->assertJson($transaction);

        // Verifica se o saldo do usuário que enviou o dinheiro foi atualizado corretamente
        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 50,
        ]);

        // Verifica se o saldo do usuário que recebeu o dinheiro foi atualizado corretamente
        $this->assertDatabaseHas('users', [
            'id' => $payee->id,
            'balance' => 50,
        ]);
    }

    /** @test */
    public function store_cannot_send_money()
    {
        // Cria um lojista e um usuário comum
        $store = User::factory()->create([
            'type' => 'store',
            'balance' => 100
        ]);
        $user = User::factory()->create([
            'type' => 'user',
            'balance' => 0
        ]);

        // Tenta realizar uma transferência de dinheiro do lojista para o usuário comum
        $response = $this->actingAs($store)->postJson('/transactions', [
            'payer' => $store->id,
            'payee' => $user->id,
            'value' => 50,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para usuário comum',
        ]);

        // Verifica se a transferência foi rejeitada com uma mensagem de erro
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Lojistas não podem enviar dinheiro para outros usuários.']);

        // Verifica se a transferência não foi criada no banco de dados
        $this->assertDatabaseMissing('transactions', [
            'payer' => $store->id,
            'payee' => $user->id,
            'value' => 50,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para usuário comum',
        ]);

        // print_r($store);
        // Verifica se os saldos dos usuários não foram atualizados
        $this->assertDatabaseHas('users', [
            'id' => $store->id,
            'balance' => $store->fresh()->balance,
        ]);

        // print_r($store);
        // print_r($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => $user->fresh()->balance,
        ]);
        // print_r($user);
    }

    /** @test */
    public function user_must_have_enough_balance_before_transfer()
    {
        // Cria um usuário comum com um saldo de 50
        $payer = User::factory()->create(['balance' => 50]);
        $payee = User::factory()->create(['balance' => 0]);

        // Tenta realizar uma transferência de dinheiro do usuário comum para outro usuário comum por um valor de 51
        $response = $this->actingAs($payer)->postJson('/transactions', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 51,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para outro usuário comum',
        ]);

        // Verifica se a transferência foi rejeitada com uma mensagem de erro
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Você não tem saldo suficiente para realizar essa transferência.']);

        // Verifica se a transferência não foi criada no banco de dados
        $this->assertDatabaseMissing('transactions', [
            'payer' => $payer->id,
            'value' => 51,
            'type' => 'd',
        ]);

        // Verifica se o saldo do usuário não foi atualizado
        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => $payer->fresh()->balance,
        ]);
    }

    /** @test */
    public function transaction_must_be_authorized_by_external_service()
    {
        // Cria dois usuários
        $payer = User::factory()->create(['balance' => 100]);
        $payee = User::factory()->create(['balance' => 0]);

        $transaction = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 50,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para outro usuário',
        ];

        // Simula o serviço autorizador externo
        Http::fake([
            'https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc' => Http::response([
                'message' => 'Autorizado',
            ], 200),
        ]);

        // Realiza a transferência de dinheiro
        $response = $this->actingAs($payer)->postJson('/transactions', $transaction);

        // Verifica se a transferência foi realizada com sucesso
        $response->assertStatus(201);
        $response->assertJson($transaction);

        // Verifica se a transferência foi criada no banco de dados
        $this->assertDatabaseHas('transactions', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 50,
            'type' => 'd',
            'description' => 'Transferência de dinheiro para outro usuário',
        ]);

        // Verifica se os saldos dos usuários foram atualizados
        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 50,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $payee->id,
            'balance' => 50,
        ]);
    }
}
