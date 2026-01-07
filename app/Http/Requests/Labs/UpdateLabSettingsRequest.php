<?php

namespace App\Http\Requests\Labs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pan_number' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'header_color' => 'nullable|string|max:20',
            'report_notes' => 'nullable|string',
            'signature_name' => 'nullable|string|max:100',
            'signature_designation' => 'nullable|string|max:100',
            'require_approval' => 'nullable|boolean', // captured via $request->has() usually, but safe to include
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Restricted to safe raster formats
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
    }
}
