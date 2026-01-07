<?php

namespace App\Http\Requests\Results;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResultRequest extends FormRequest
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
            'value' => ['nullable', 'string'],
            'edit_reason' => ['required', 'string', 'max:255'],
            'parameters' => ['nullable', 'array'],
            'parameters.*.value' => ['nullable', 'string'],
            'parameters.*.numeric_value' => ['nullable', 'numeric'],
        ];
    }
}
