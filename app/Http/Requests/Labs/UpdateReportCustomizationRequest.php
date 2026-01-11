<?php

namespace App\Http\Requests\Labs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportCustomizationRequest extends FormRequest
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
            'signature_width' => 'nullable|integer|min:50|max:200',
            'signature_height' => 'nullable|integer|min:20|max:80',
            'signature_name_2' => 'nullable|string|max:100',
            'signature_designation_2' => 'nullable|string|max:100',
            'signature_width_2' => 'nullable|integer|min:50|max:200',
            'signature_height_2' => 'nullable|integer|min:20|max:80',
            'report_notes' => 'nullable|string',
            'headerless_margin_top' => 'nullable|integer|min:10|max:60',
            'headerless_margin_bottom' => 'nullable|integer|min:10|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'signature_image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
    }
}
