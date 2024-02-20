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

}
