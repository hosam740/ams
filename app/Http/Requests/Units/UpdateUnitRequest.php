<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\assets\Unit;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'type'         => ['sometimes', Rule::in(self::getTypeValues())],
            'description'  => ['sometimes', 'string', 'max:1000'],
            'area'         => ['sometimes', 'numeric', 'min:1'],
            'status'       => ['sometimes', Rule::in(self::getStatusValues())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'اسم الوحدة مطلوب.',
            'name.string'          => 'اسم الوحدة يجب أن يكون نصًا.',
            'name.max'             => 'اسم الوحدة يجب ألا يتجاوز 255 حرفًا.',

            'type.in'              => 'نوع الوحدة غير صالح.',

            'description.string'   => 'الوصف يجب أن يكون نصًا.',
            'description.max'      => 'الوصف يجب ألا يتجاوز 1000 حرف.',

            'area.numeric'         => 'المساحة يجب أن تكون رقمًا.',
            'area.min'             => 'المساحة يجب ألا تقل عن 1 متر مربع.',

            'status.in'            => 'حالة الوحدة غير صالحة.',
        ];
    }

    protected static function typeValues(): array
    {
        return Unit::getTypeValues();
    }

    protected static function statusValues(): array
    {
        return Unit::getStatusValues();
    }
}
