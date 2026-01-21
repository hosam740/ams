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
            'end_date'       => ['required', 'date', 'after:beginning_date', 'after_or_equal:today',
                                function ($attribute, $value, $fail) {$this->validateEndDate($attribute, $value, $fail);}],// rule to ensure that end_date is exactly N months after beginning_date minus 1 day
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
            'end_date.after_or_equal' => 'لا يمكن إنشاء عقد منتهي.',

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


    /**
 * Validate that end_date is exactly N months after beginning_date minus 1 day.
 * 
 * Example:
 *   beginning_date = Jan 15
 *   valid end_dates = Feb 14, Mar 14, Apr 14...
 */
    protected function validateEndDate($attribute, $value, $fail)
    {
        if (empty($this->beginning_date)) {
        return;
    }

    $beginning = Carbon::parse($this->beginning_date);
    $end = Carbon::parse($value);

    // Add 1 day to end_date for correct month calculation
    $diffInMonths = $beginning->diffInMonths($end->copy()->addDay());

    if ($diffInMonths < 1) {
        $fail('مدة العقد يجب أن تكون شهرا واحد على الأقل.');
        return;
    }

    // Expected: beginning + N months - 1 day
    $expectedEnd = $beginning->copy()->addMonths($diffInMonths)->subDay();

    if (!$end->equalTo($expectedEnd)) {
        $suggestions = [];
        for ($i = 1; $i <= 3; $i++) {
            $suggestions[] = $beginning->copy()
                ->addMonths($i)
                ->subDay()
                ->format('Y-m-d');
        }

        $fail('تاريخ النهاية غير صحيح. تواريخ مقترحة: ' . implode('، ', $suggestions));
        }
    }
}
