<?php

namespace App\Http\Requests\TestParameters;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestParameterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
            'normal_min' => ['nullable', 'numeric'],
            'normal_max' => ['nullable', 'numeric'],
            'normal_min_male' => ['nullable', 'numeric'],
            'normal_max_male' => ['nullable', 'numeric'],
            'normal_min_female' => ['nullable', 'numeric'],
            'normal_max_female' => ['nullable', 'numeric'],
            'critical_low' => ['nullable', 'numeric'],
            'critical_high' => ['nullable', 'numeric'],
            // Strict formula validation: allow only numbers, operators, and {CODE} placeholders
            'formula' => ['nullable', 'string', 'max:255', 'regex:/^([0-9\+\-\*\/\(\)\.\s]|(\{[\w]+\}))+$/'],
            'group_name' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
