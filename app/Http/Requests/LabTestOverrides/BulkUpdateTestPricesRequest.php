<?php

namespace App\Http\Requests\LabTestOverrides;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateTestPricesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prices' => 'required|array',
            'prices.*.test_id' => 'required|exists:tests,id',
            'prices.*.price' => 'required|numeric|min:0',
        ];
    }
}
