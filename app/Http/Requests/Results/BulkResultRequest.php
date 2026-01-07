<?php

namespace App\Http\Requests\Results;

use Illuminate\Foundation\Http\FormRequest;

class BulkResultRequest extends FormRequest
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
            'results' => ['required', 'array'],
            'results.*.id' => ['required', 'exists:results,id'],
            'results.*.value' => ['required', 'string'],
            'results.*.numeric_value' => ['nullable', 'numeric'],
        ];
    }
}
