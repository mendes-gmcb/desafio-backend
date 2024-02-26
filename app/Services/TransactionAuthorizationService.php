<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TransactionAuthorizationService
{
    public function __construct() {}
    
    public function authorize(string $url): array
    {
        try {
            $response = Http::get($url);
        } catch (\Exception $e) {
            throw new \Exception('Ocorreu um erro ao consultar o serviço autorizador externo.', 500);
        }

        if ($response->status() != 200) {
            throw new \Exception('Ocorreu um erro ao consultar o serviço autorizador externo.', 500);
        }

        $authorization = $response->json();

        if ($authorization['message'] != 'Autorizado') {
            throw new \Exception('A transferência não foi autorizada pelo serviço autorizador externo.', 403);
        }

        return $authorization;
    }
}
