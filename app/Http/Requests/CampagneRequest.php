<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampagneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre'              => 'required|string|max:255',
            'description'        => 'required|string',
            'objectif_financier' => 'required|numeric|min:100',
            'categorie_id'       => 'required|exists:categories,id',
        ];
    }


    public function messages(): array
    {
        return [
            'titre.required'              => 'Le titre est obligatoire',
            'titre.max'                   => 'Le titre ne doit pas dépasser 255 caractères',
            'description.required'        => 'La description est obligatoire',
            'objectif_financier.required' => 'L\'objectif financier est obligatoire',
            'objectif_financier.numeric'  => 'L\'objectif financier doit être un nombre',
            'objectif_financier.min'      => 'L\'objectif financier doit être au moins 100',
            'categorie_id.required'       => 'La catégorie est obligatoire',
            'categorie_id.exists'         => 'La catégorie sélectionnée est invalide',
        ];
    }
}
