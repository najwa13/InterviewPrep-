<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du domaine est obligatoire.',
            'color.required' => 'Choisissez une couleur pour ce domaine.',
            'color.regex' => 'La couleur doit être un code hexadécimal valide (ex: #3B82F6).',
        ];
    }
}