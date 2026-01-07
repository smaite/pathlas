<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class AddPaymentRequest extends FormRequest
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
        // Get the booking from the route
        $booking = $this->route('booking');
        $maxAmount = $booking ? $booking->due_amount : null;

        $rules = [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,upi,bank_transfer,other'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($maxAmount !== null) {
            $rules['amount'][] = 'max:' . $maxAmount;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'amount.max' => 'The payment amount cannot exceed the due amount.',
        ];
    }
}
