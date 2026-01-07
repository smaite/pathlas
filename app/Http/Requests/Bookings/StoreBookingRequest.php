<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'patient_id' => ['required', 'exists:patients,id'],
            'tests' => ['required', 'array', 'min:1'],
            'tests.*' => ['exists:tests,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_urgent' => ['boolean'],
            'referring_doctor' => ['nullable', 'string', 'max:255'],
            'patient_type' => ['nullable', 'string', 'max:50'],
            'collection_centre' => ['nullable', 'string', 'max:255'],
            'collection_date' => ['nullable', 'date'],
            'received_date' => ['nullable', 'date'],
            'reporting_date' => ['nullable', 'date'],
            'sample_collected_by' => ['nullable', 'string', 'max:255'],
            'sample_collected_at' => ['nullable', 'string', 'max:500'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,card,upi,bank_transfer,other'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_urgent' => $this->boolean('is_urgent'),
        ]);
    }
}
