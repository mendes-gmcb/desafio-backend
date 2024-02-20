<?php

namespace Tests\Unit;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Transaction;
use App\Models\User;
use SebastianBergmann\Type\VoidType;
use Tests\TestCase;

class BaseTest extends TestCase
{
    /** @test */
    public function validate_users_model(): void
    {
        // Criar uma instância do modelo User
        $user = new User();
        $fillable = $user->getFillable();

        $expected = array(
            'id',
            'type',
            'name',
            'email',
            'cpf_cnpj',
            'password',
            'email_verified_at',
            'remember_token',
            'balance',
            'limit',
            'created_at',
            'updated_at',
            'deleted_at'
        );

        // Verificar se todos os atributos estão presentes
        $this->assertEquals($expected, $fillable, "Campos da tabela user diferentes do esperado");
    }

    /** @test */
    public function validate_transaction_model(): void
    {
        // Criar uma instância do modelo User
        $transaction = new Transaction();
        $fillable = $transaction->getFillable();

        $expected = array(
            'id',
            'payer',
            'payee',
            'value',
            'type',
            'description',
            'created_at',
            'updated_at',
            'deleted_at'
        );

        // Verificar se todos os atributos estão presentes
        $this->assertEquals($expected, $fillable, "Campos da tabela transaction diferentes do esperado");
    }
}