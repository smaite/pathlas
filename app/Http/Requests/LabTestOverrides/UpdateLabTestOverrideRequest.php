<?php

namespace App\Http\Requests\LabTestOverrides;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabTestOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => 'nullable|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'normal_range' => 'nullable|string|max:100',
            'sample_type' => 'nullable|string|max:100',
            'method' => 'nullable|string|max:255',
            'turnaround_time' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
