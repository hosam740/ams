<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateTenantRequest extends FormRequest
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
        // لو تستخدم Route Model Binding: Route::put('tenants/{tenant}', ...)
        // تقدر تجيب المعرّف من: $this->route('tenant')
        $tenantId = optional($this->route('tenant'))->id;

        return [
            'first_name'   => ['sometimes', 'required', 'string', 'max:50'],
            'last_name'    => ['sometimes', 'required', 'string', 'max:50'],
            'national_id'  => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('tenants', 'national_id')->ignore($tenantId),
            ],
            'phone_number' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'nationality'  => ['sometimes', 'required', 'string', 'max:50'],
        ];
    }
}
