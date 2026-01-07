<?php

namespace App\Http\Requests\TestPackages;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestPackageRequest extends FormRequest
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
        $packageId = $this->route('package') ? $this->route('package')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:test_packages,code,' . $packageId],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'tests' => ['required', 'array', 'min:1'],
            'tests.*' => ['exists:tests,id'],
            'is_active' => ['boolean'],
        ];
    }
}
