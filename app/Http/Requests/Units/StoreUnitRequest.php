<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\assets\Unit;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
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
            //
           'name'         => 'required|string|max:255',
            'type'         => ['required', Rule::in(self::getTypeValues())],
            'description'  => ['required', 'string', 'max:1000'],
            'area'         => ['required', 'numeric', 'min:1'],
            'status'       => ['required', Rule::in(self::getStatusValues())],
            'property_id'  => ['required', 'exists:properties,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'اسم الوحدة مطلوب.',
            'name.string'          => 'اسم الوحدة يجب أن يكون نصًا.',
            'name.max'             => 'اسم الوحدة يجب ألا يتجاوز 255 حرفًا.',

            'type.required'        => 'نوع الوحدة مطلوب.',
            'type.in'              => 'نوع الوحدة غير صالح.',

            'description.required' => 'الوصف مطلوب.',
            'description.string'   => 'الوصف يجب أن يكون نصًا.',
            'description.max'      => 'الوصف يجب ألا يتجاوز 1000 حرف.',

            'area.required'        => 'المساحة مطلوبة.',
            'area.numeric'         => 'المساحة يجب أن تكون رقمًا.',
            'area.min'             => 'المساحة يجب ألا تقل عن 1 متر مربع.',

            'status.required'      => 'حالة الوحدة مطلوبة.',
            'status.in'            => 'حالة الوحدة غير صالحة.',

            'property_id.required' => 'العقار المرتبط مطلوب.',
            'property_id.exists'   => 'العقار المحدد غير موجود.',
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
