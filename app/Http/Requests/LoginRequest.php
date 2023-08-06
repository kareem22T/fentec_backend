<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'emailorphone' => 'required|unique',
            'password' => 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'emailorphone.required' => 'please enter your email or phone number',
            'password.required' => 'please enter your password',
        ];
    }
}
