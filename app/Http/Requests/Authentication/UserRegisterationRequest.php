<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'regex:/^05\d{8}$/', 'unique:users,phone_number'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'الاسم مطلوب.',
            'name.string'    => 'الاسم يجب أن يكون نصاً.',
            'name.max'       => 'الاسم يجب ألا يتجاوز 255 حرفاً.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email'    => 'تنسيق البريد الإلكتروني غير صحيح.',
            'email.unique'   => 'هذا البريد الإلكتروني مسجل مسبقاً.',

            'phone_number.required' => 'رقم الجوال مطلوب.',
            'phone_number.regex' => 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'phone_number.unique'   => 'رقم الجوال مسجل مسبقاً.',

            'password.required'  => 'كلمة المرور مطلوبة.',
            'password.string'    => 'كلمة المرور يجب أن تكون نصاً.',
            'password.min'       => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'الاسم',
            'email'    => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
        ];
    }
}
