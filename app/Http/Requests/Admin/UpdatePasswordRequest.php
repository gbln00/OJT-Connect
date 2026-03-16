<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'      => 'Current password is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required'              => 'New password is required.',
            'password.confirmed'             => 'Passwords do not match.',
        ];
    }
}