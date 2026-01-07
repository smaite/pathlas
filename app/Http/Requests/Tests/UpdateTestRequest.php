<?php

namespace App\Http\Requests\Tests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestRequest extends FormRequest
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
        $testId = $this->route('test') ? $this->route('test')->id : null;

        return [
            'category_id' => ['required', 'exists:test_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', Rule::unique('tests', 'code')->ignore($testId)],
            'short_name' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
            'normal_range_male' => ['nullable', 'string'],
            'normal_range_female' => ['nullable', 'string'],
            'normal_min' => ['nullable', 'numeric'],
            'normal_max' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric', 'min:0'],
            'sample_type' => ['nullable', 'in:blood,urine,stool,swab,other'],
            'method' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'interpretation' => ['nullable', 'string'],
            'turnaround_time' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
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

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }
    }
}
