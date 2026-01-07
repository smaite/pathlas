<?php

namespace App\Http\Requests\TestParameters;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTestParametersRequest extends FormRequest
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
            'parameters' => ['required', 'array'],
            'parameters.*.id' => ['required', 'integer', 'exists:test_parameters,id'],
            'parameters.*.sort_order' => ['required', 'integer', 'min:0'],
            'parameters.*.group_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
