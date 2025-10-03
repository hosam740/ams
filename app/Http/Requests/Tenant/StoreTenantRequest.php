<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
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
            'first_name'   => ['required', 'string', 'max:50'],
            'last_name'    => ['required', 'string', 'max:50'],
            'national_id'  => ['required', 'regex:/^\d{10}$/', 'unique:tenants,national_id'],
            'phone_number' => ['required', 'regex:/^05\d{8}$/', 'unique:tenants,phone_number'],
            'nationality'  => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'   => 'الاسم الأول مطلوب.',
            'first_name.max'        => 'الاسم الأول يجب ألا يتجاوز :max حرفًا.',
        
            'last_name.required'    => 'اسم العائلة مطلوب.',
            'last_name.max'         => 'اسم العائلة يجب ألا يتجاوز :max حرفًا.',
        
            'national_id.required'  => 'رقم الهوية الوطنية/الاقامة مطلوب.',
            'national_id.unique'    => 'رقم الهوية الوطنية/الاقامة مسجّل مسبقًا.',
            'national_id.regex'     => 'رقم الهوية يجب أن يتكون من 10 أرقام.',
        
            'phone_number.required' => 'رقم الهاتف مطلوب.',
            'phone_number.regex'    => 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'phone_number.unique'   => 'رقم الجوال مسجل مسبقاً.',
        
            'nationality.required'  => 'الجنسية مطلوبة.',
            'nationality.max'       => 'الجنسية يجب ألا تتجاوز :max حرفًا.',
        ]; 
    }

    public function attributes(): array
    {
        return [
            'first_name'   => 'الاسم الأول',
            'last_name'    => 'اسم العائلة',
            'national_id'  => 'رقم الهوية الوطنية/الاقامة',
            'phone_number' => 'رقم الهاتف',
            'nationality'  => 'الجنسية',
        ];
    }
}
