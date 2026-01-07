<?php

namespace App\Http\Requests\Labs;

use Illuminate\Foundation\Http\FormRequest;

class PreviewReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'header_color' => 'nullable|string|max:20',
            'logo_width' => 'nullable|integer|min:30|max:200',
            'logo_height' => 'nullable|integer|min:30|max:150',
            'signature_name' => 'nullable|string|max:100',
            'signature_designation' => 'nullable|string|max:100',
            'signature_name_2' => 'nullable|string|max:100',
            'signature_designation_2' => 'nullable|string|max:100',
            'report_notes' => 'nullable|string',
            'headerless_margin_top' => 'nullable|integer|min:10|max:100',
            'headerless_margin_bottom' => 'nullable|integer|min:10|max:80',
            'showHeader' => 'nullable|boolean',
        ];
    }
}
