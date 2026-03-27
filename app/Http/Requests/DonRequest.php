<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant'     => 'required|numeric|min:10',
            'campagne_id' => 'required|exists:campagnes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required'     => 'Le montant est obligatoire',
            'montant.numeric'      => 'Le montant doit être un nombre',
            'montant.min'          => 'Le montant minimum est 10',
            'campagne_id.required' => 'La campagne est obligatoire',
            'campagne_id.exists'   => 'La campagne sélectionnée est invalide',
        ];
    }
}