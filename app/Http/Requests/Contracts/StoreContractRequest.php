<?php

namespace App\Http\Requests\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Contract;
use Carbon\Carbon;

class StoreContractRequest extends FormRequest
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
            'tenant_id'      => ['required', 'integer',
                // لو مفعّل SoftDeletes على tenants خلك على whereNull('deleted_at')
                Rule::exists('tenants', 'id')->whereNull('deleted_at')],

            'unit_id'        => ['required', 'integer', Rule::exists('units', 'id')],
            'beginning_date' => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after:beginning_date', 
                                function ($attribute, $value, $fail) {$this->validateIsLastOfMonth($attribute, $value, $fail);}],// rule to ensure that end_date is last day in the mounth
            //'ended_at'     => ['nullable', 'date', 'after_or_equal:end_date'],
            'total_amount'   => ['required', 'numeric', 'min:0.01'],
            'payment_plan'   => ['required', Rule::in(Contract::getPaymentPlanValues())],
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_id.required'      => 'حقل المستأجر مطلوب.',
            'tenant_id.exists'        => 'المستأجر المحدد غير موجود أو مؤرشف.',

            'unit_id.required'        => 'الوحدة مطلوبة.',
            'unit_id.exists'          => 'الوحدة المحددة غير موجودة أو مؤرشفة.',

            'beginning_date.required' => 'تاريخ بداية العقد مطلوب.',
            'beginning_date.date'     => 'تنسيق تاريخ بداية العقد غير صحيح.',

            'end_date.required'       => 'تاريخ نهاية العقد مطلوب.',
            'end_date.date'           => 'تنسيق تاريخ نهاية العقد غير صحيح.',
            'end_date.after'          => 'تاريخ نهاية العقد يجب أن يكون بعد تاريخ البداية.',

            //'ended_at.date'           => 'تنسيق تاريخ الإنهاء غير صحيح.',
            //'ended_at.after_or_equal' => 'تاريخ الإنهاء يجب أن يكون بعد أو يساوي تاريخ النهاية.',

            'total_amount.required'   => 'المبلغ الإجمالي مطلوب.',
            'total_amount.numeric'    => 'المبلغ الإجمالي يجب أن يكون رقمًا.',
            'total_amount.min'        => 'المبلغ الإجمالي يجب أن يكون أكبر من صفر.',
            
            'payment_plan.required'   => 'خطة الدفع مطلوبة.',
            'payment_plan.in'         => 'خطة الدفع غير مسموح بها.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id'      => 'المستأجر',
            'unit_id'        => 'الوحدة',
            'beginning_date' => 'تاريخ البداية',
            'end_date'       => 'تاريخ النهاية',
            'ended_at'       => 'تاريخ الإنهاء',
            'total_amount'   => 'المبلغ الإجمالي',
            'payment_plan'   => 'خطة الدفع',
            'active'         => 'التفعيل',
        ];
    }


    // function to ensure that end_date is last day in the mounth
    protected function validateIsLastOfMonth($attribute, $value, $fail)
    {
        if (!Carbon::parse($value)->isLastOfMonth()) {
            $fail('تاريخ نهاية العقد يجب أن يكون آخر يوم في الشهر.');
        }
    }
}
