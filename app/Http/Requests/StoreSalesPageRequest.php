<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesPageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'key_features' => ['required', 'array', 'min:1'],
            'key_features.*' => ['required', 'string', 'max:255'],
            'target_audience' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'unique_selling_points' => ['nullable', 'array'],
            'unique_selling_points.*' => ['required', 'string', 'max:255'],
        ];
    }
}
