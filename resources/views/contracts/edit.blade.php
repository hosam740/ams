@extends('layouts.app')

@section('title', 'تعديل العقد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">تعديل العقد</h5>
    </div>

    <div class="card-body">
        {{-- رسائل الأخطاء --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contracts.update', $contract) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- المستأجر (للعرض فقط) --}}
            <div class="mb-3">
                <label class="form-label">المستأجر</label>
                <input type="text" class="form-control"
                       value="{{ $contract->tenant->first_name }} {{ $contract->tenant->last_name }} — {{ $contract->tenant->national_id }}"
                       disabled>
            </div>

            {{-- الوحدة (للعرض فقط) --}}
            <div class="mb-3">
                <label class="form-label">الوحدة</label>
                <input type="text" class="form-control"
                       value="{{ $contract->unit->name ?? 'وحدة #' . $contract->unit->id }} — {{ ucfirst($contract->unit->type) }} — {{ $contract->unit->area }} م² — {{ $contract->unit->property->asset->name ?? '' }}"
                       disabled>
            </div>

            <div class="row g-3">
                {{-- تاريخ البداية --}}
                <div class="col-md-6">
                    <label for="beginning_date" class="form-label">تاريخ البداية</label>
                    <input type="date" name="beginning_date" id="beginning_date"
                           class="form-control @error('beginning_date') is-invalid @enderror"
                           value="{{ old('beginning_date', $contract->beginning_date) }}" required>
                    @error('beginning_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- تاريخ النهاية --}}
                <div class="col-md-6">
                    <label for="end_date" class="form-label">تاريخ النهاية</label>
                    <input type="date" name="end_date" id="end_date"
                           class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date', $contract->end_date) }}" required>
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- المبلغ الإجمالي --}}
            <div class="mb-3 mt-3">
                <label for="total_amount" class="form-label">المبلغ الإجمالي</label>
                <div class="input-group">
                    <input type="number" name="total_amount" id="total_amount" min="0.01" step="0.01"
                           class="form-control @error('total_amount') is-invalid @enderror"
                           value="{{ old('total_amount', $contract->total_amount) }}" required>
                    <span class="input-group-text">ر.س</span>
                    @error('total_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- خطة الدفع --}}
            <div class="mb-3">
                <label for="payment_plan" class="form-label">خطة الدفع</label>
                <select name="payment_plan" id="payment_plan"
                        class="form-select @error('payment_plan') is-invalid @enderror" required>
                    <option value="">-- اختر خطة الدفع --</option>
                    @php
                        $paymentPlanLabels = [
                            'monthly'    => 'شهري',
                            'quarterly'  => 'ربع سنوي',
                            'triannual'  => 'ثلث سنوي',
                            'semiannual' => 'نصف سنوي',
                            'annually'   => 'سنوي',
                        ];
                    @endphp
                    @foreach (\App\Models\Contract::getPaymentPlanValues() as $plan)
                        <option value="{{ $plan }}" {{ old('payment_plan', $contract->payment_plan) == $plan ? 'selected' : '' }}>
                            {{ $paymentPlanLabels[$plan] ?? ucfirst(str_replace('_', ' ', $plan)) }}
                        </option>
                    @endforeach
                </select>
                @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-success">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

{{-- تحسين: اجعل تاريخ النهاية دائماً بعد البداية --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const begin = document.getElementById('beginning_date');
    const end   = document.getElementById('end_date');

    function syncMin() {
        if (begin.value) {
            end.min = begin.value;
            if (end.value && end.value < begin.value) end.value = begin.value;
        } else {
            end.removeAttribute('min');
        }
    }

    begin.addEventListener('change', syncMin);
    syncMin();
});
</script>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }
</style>
@endsection
