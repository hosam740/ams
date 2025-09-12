<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // لازم true عشان يسمح بالتحقق
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email'    => 'تنسيق البريد الإلكتروني غير صحيح.',
            'email.exists'   => 'هذا البريد الإلكتروني غير مسجل.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string'   => 'كلمة المرور يجب أن تكون نصاً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
        ];
    }
}
