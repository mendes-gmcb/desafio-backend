<?php

namespace Database\Seeders;

use App\Models\User;
use Avlima\PhpCpfCnpjGenerator\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
                'id' => "9b6a1709-38ff-4f16-8044-a89bcb6d8457",
                'name' => "maria",
                'email' => "maria@gmail.com",
                'email_verified_at' => now(),
                'cpf_cnpj' => Generator::cpf(true),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'balance' => 10000,
                'limit' => 200000
        ]);
        
        User::create([
                'id' => "9b689cec-cb98-4575-9894-8c0cddbb111b",
                'name' => "gabriel",
                'email' => "gabriel@gmail.com",
                'email_verified_at' => now(),
                'cpf_cnpj' => Generator::cpf(true),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
        ]);

        User::create([
                'id' => "9b689cec-cb98-4575-9894-8c0cddbb111a",
                'name' => "gabriel-loja",
                'type' => "store",
                'email' => "loja@gmail.com",
                'email_verified_at' => now(),
                'cpf_cnpj' => Generator::cnpj(true),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
        ]);
    }
}
