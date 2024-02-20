<?php

namespace Tests\Feature\Auth;

use Avlima\PhpCpfCnpjGenerator\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpf_cnpj' => Generator::cpf(true),
            'password' => 'password',
            'password_confirmation' => 'password',
            'balance' => 0,
            'limit' => 100000,
        ]);

        $this->assertAuthenticated();
        $response->assertNoContent();
    }
}
