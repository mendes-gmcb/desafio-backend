<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        try{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'cpf_cnpj' => ['required', 'string', 'lowercase', 'cpf_ou_cnpj', 'formato_cpf_ou_cnpj', 'unique:'.User::class],
                'balance' => ['required', 'int'],
                'limit' => ['required', 'int']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // return response()->json($e->errors(), 400);
            dd($e->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf_cnpj' => $request->cpf_cnpj,
            'balance' => $request->balance,
            'limit' => $request->limit
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
