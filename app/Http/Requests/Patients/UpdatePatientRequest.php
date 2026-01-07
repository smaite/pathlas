<?php

namespace App\Http\Requests\Patients;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', 'in:male,female,other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'blood_group' => ['nullable', 'string', 'max:5', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'medical_history' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
