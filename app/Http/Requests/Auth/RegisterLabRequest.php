<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterLabRequest extends FormRequest
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
            'lab_name' => ['required', 'string', 'max:255', 'min:3'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'lab_name.required' => 'The lab name is required.',
            'email.unique' => 'This email is already registered.',
            'password.mixed' => 'The password must contain at least one uppercase and one lowercase letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'phone.regex' => 'The phone format is invalid.',
        ];
    }
}
