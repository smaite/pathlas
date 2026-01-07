<?php

namespace App\Http\Requests\Tests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
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
            'category_id' => ['required', 'exists:test_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'unique:tests,code'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
            'normal_range_male' => ['nullable', 'string'],
            'normal_range_female' => ['nullable', 'string'],
            'normal_min' => ['nullable', 'numeric'],
            'normal_max' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric', 'min:0'],
            'sample_type' => ['required', 'in:blood,urine,stool,swab,other'],
            'method' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'interpretation' => ['nullable', 'string'],
            'turnaround_time' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize rich text fields to prevent XSS
        $allowedTags = '<b><i><u><br><p><ul><li><strong><em>';

        if ($this->has('instructions')) {
            $this->merge([
                'instructions' => strip_tags($this->instructions ?? '', $allowedTags),
            ]);
        }

        if ($this->has('interpretation')) {
            $this->merge([
                'interpretation' => strip_tags($this->interpretation ?? '', $allowedTags),
            ]);
        }
    }
}
