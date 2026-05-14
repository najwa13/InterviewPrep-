<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'explanation' => ['required', 'string', 'min:20'],
            'difficulty' => ['required', 'in:junior,mid,senior'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du concept est obligatoire.',
            'explanation.required' => "L'explication est obligatoire.",
            'explanation.min' => "L'explication doit faire au moins 20 caractères.",
            'difficulty.required' => 'Choisissez un niveau de difficulté.',
            'difficulty.in' => 'Le niveau doit être junior, mid ou senior.',
        ];
    }
}