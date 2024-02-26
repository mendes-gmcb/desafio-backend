<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactMoneyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->id() === $this->payer) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payer' => ['required', 'uuid'],
            'payee' => ['required', 'uuid'],
            'value' => ['required', 'int', 'min:1'],
            'type' => ['required', 'in:c,d,p'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payer.required' => 'O atributo payer é obrigatório',
            'payer.uuid' => 'O atributo payer precisa ser um uuid',
            'payee.required' => 'O atributo payee é obrigatório',
            'payee.uuid' => 'O atributo payer precisa ser um uuid',
            'value.required' => 'O atributo value é obrigatório',
            'value.int' => 'O atributo value precisa ser um valor inteiro',
            'value.min' => 'O atributo value precisa ser no mínimo 1 centavo',
            'type.required' => 'O atributo type é obrigatório',
            'type.in' => 'O atributo type é precisa ser "d" para débito, "p" para pix ou "c" para crédito',
            'description.required' => 'O atributo description é obrigatório',
            'description.string' => 'O atributo description precisa ser um texto',
            'description.min' => 'O atributo descrition precisa conter no mínimo 3 caracteres',
            'description.max' => 'O atributo descrition precisa conter no máximo 255 caracteres',
        ];
    }
}
