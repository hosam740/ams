<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarkPaymentAsPaidRequest extends FormRequest
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
            'paid_amount'    => ['required', 'numeric', 'min:1'],
            'paid_date'      => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'string', Rule::in(Payment::getPaymentMethods())],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'paid_amount.required'      => 'المبلغ المدفوع مطلوب.',
            'paid_amount.numeric'       => 'المبلغ المدفوع يجب أن يكون رقماً.',
            'paid_amount.min'           => 'المبلغ المدفوع يجب أن يكون 1 أو أكثر.',

            'paid_date.required'        => 'تاريخ السداد مطلوب.',
            'paid_date.date'            => 'تاريخ السداد غير صالح.',
            'paid_date.before_or_equal' => 'تاريخ السداد لا يمكن أن يكون في المستقبل.',

            'payment_method.required'   => 'طريقة الدفع مطلوبة.',
            'payment_method.in'         => 'طريقة الدفع غير صالحة.',

            'notes.max'                 => 'الملاحظات يجب ألا تتجاوز :max حرفاً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'paid_amount'    => 'المبلغ المدفوع',
            'paid_date'      => 'تاريخ السداد',
            'payment_method' => 'طريقة الدفع',
            'notes'          => 'الملاحظات',
        ];
    }
}
