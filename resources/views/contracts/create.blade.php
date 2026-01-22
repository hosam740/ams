@extends('layouts.app')

@section('title', 'إضافة عقد جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">إضافة عقد جديد</h5>
    </div>

    <div class="card-body">
        {{-- رسائل النجاح/الأخطاء --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contracts.store') }}" method="POST">
            @csrf

            {{-- المستأجر --}}
            <div class="mb-3">
                <label for="tenant_id" class="form-label">المستأجر</label>
                <select name="tenant_id" id="tenant_id"
                        class="form-select @error('tenant_id') is-invalid @enderror" required>
                    <option value="">-- اختر المستأجر --</option>
                    @foreach (\App\Models\Tenant::orderBy('first_name')->orderBy('last_name')->get() as $tenant)
                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            <bdi>{{ $tenant->first_name }} {{ $tenant->last_name }}</bdi> — <bdi>{{ $tenant->national_id }}</bdi>
                        </option>
                    @endforeach
                </select>
                @error('tenant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- الوحدة --}}
            <div class="mb-3">
                <label for="unit_id" class="form-label">الوحدة</label>
                <select name="unit_id" id="unit_id"
                        class="form-select @error('unit_id') is-invalid @enderror" required>
                    <option value="">-- اختر الوحدة --</option>
                    @php
                        $selectedUnitId = old('unit_id') ?? request()->query('unit_id');
                    @endphp
                    @foreach (
                        \App\Models\assets\Unit::query()
                            ->whereHas('property.asset', fn($q) => $q->where('manager_id', Auth::id()))
                            ->where('status', 'available')
                            ->with(['property.asset'])
                            ->orderBy('id', 'desc')
                            ->get() as $unit
                    )
                        <option value="{{ $unit->id }}" {{ $selectedUnitId == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name ?? 'وحدة #' . $unit->id }}
                            — {{ ucfirst($unit->type) }}
                            — {{ $unit->area }} م²
                            — {{ $unit->property->city ?? '—' }} / {{ $unit->property->neighborhood ?? '—' }}
                            @if($unit->property?->asset?->name) — ({{ $unit->property->asset->name }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
                {{-- تاريخ البداية --}}
                <div class="col-md-6">
                    <label for="beginning_date" class="form-label">تاريخ البداية</label>
                    <input type="date" name="beginning_date" id="beginning_date"
                           class="form-control @error('beginning_date') is-invalid @enderror"
                           value="{{ old('beginning_date') }}" required>
                    @error('beginning_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- تاريخ النهاية --}}
                <div class="col-md-6">
                    <label for="end_date" class="form-label">تاريخ النهاية</label>
                    <input type="date" name="end_date" id="end_date"
                           class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}" required>
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="end_date_validation_error" class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>

            {{-- المبلغ الإجمالي --}}
            <div class="mb-3 mt-3">
                <label for="total_amount" class="form-label">المبلغ الإجمالي</label>
                <div class="input-group">
                    <input type="number" name="total_amount" id="total_amount" min="0.01" step="0.01"
                           class="form-control @error('total_amount') is-invalid @enderror"
                           value="{{ old('total_amount') }}" required>
                    <span class="input-group-text">SAR</span>
                    @error('total_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- خطة الدفع --}}
            <div class="mb-3">
                <label for="payment_plan" class="form-label">خطة الدفع</label>
                <select name="payment_plan" id="payment_plan"
                        class="form-select @error('payment_plan') is-invalid @enderror" required>
                    <option value="">-- اختر خطة الدفع --</option>
                    @foreach (\App\Models\Contract::getPaymentPlanValues() as $plan)
                        <option value="{{ $plan }}" {{ old('payment_plan') == $plan ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $plan)) }}
                        </option>
                    @endforeach
                </select>
                @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- (اختياري) تفعيل العقد مستقبلاً إذا أضفت الحقل --}}
            {{-- <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="active" name="active"
                       {{ old('active') ? 'checked' : '' }}>
                <label class="form-check-label" for="active">تفعيل العقد</label>
            </div> --}}

            <div class="d-flex justify-content-between">
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>

{{-- تحسين: التحقق من تاريخ النهاية - مضاعفات الشهور فقط --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const beginInput = document.getElementById('beginning_date');
    const endInput = document.getElementById('end_date');
    const errorDiv = document.getElementById('end_date_validation_error');
    const form = endInput.closest('form');

    /**
     * حساب التواريخ الصحيحة لنهاية العقد
     * القاعدة: تاريخ البداية + N أشهر - يوم واحد
     */
    function calculateValidEndDates(startDateStr, maxMonths = 60) {
        if (!startDateStr) return [];

        const dates = [];
        const startDate = new Date(startDateStr);

        for (let months = 1; months <= maxMonths; months++) {
            const validDate = new Date(startDate);
            validDate.setMonth(validDate.getMonth() + months);
            validDate.setDate(validDate.getDate() - 1);
            dates.push(validDate.toISOString().split('T')[0]);
        }

        return dates;
    }

    /**
     * التحقق من صحة تاريخ النهاية
     * @returns {boolean} true إذا كان التاريخ صحيح، false إذا كان خاطئ
     */
    function isEndDateValid() {
        const beginValue = beginInput.value;
        const endValue = endInput.value;

        if (!beginValue || !endValue) {
            return true; // سيتم التحقق من required بواسطة HTML5
        }

        const validDates = calculateValidEndDates(beginValue, 60);
        return validDates.includes(endValue);
    }

    /**
     * التحقق من صحة تاريخ النهاية وعرض رسالة تحذير إذا لزم الأمر
     */
    function validateEndDate() {
        const beginValue = beginInput.value;
        const endValue = endInput.value;

        // إخفاء الرسالة إذا لم يتم اختيار تاريخ بداية أو نهاية
        if (!beginValue || !endValue) {
            errorDiv.style.display = 'none';
            endInput.classList.remove('is-invalid');
            return;
        }

        const validDates = calculateValidEndDates(beginValue, 60);

        // التحقق: هل التاريخ المختار ضمن التواريخ الصحيحة؟
        if (validDates.includes(endValue)) {
            // التاريخ صحيح
            errorDiv.style.display = 'none';
            endInput.classList.remove('is-invalid');
        } else {
            // التاريخ خاطئ - عرض رسالة تحذير
            const suggestions = validDates.slice(0, 3).join('، ');
            errorDiv.textContent = 'التاريخ غير صحيح. اختر تاريخ مثل: ' + suggestions;
            errorDiv.style.display = 'block';
            endInput.classList.add('is-invalid');
        }
    }

    /**
     * عند تغيير تاريخ البداية: تعيين الحد الأدنى لتاريخ النهاية والتحقق
     */
    function onBeginDateChange() {
        const beginValue = beginInput.value;

        if (beginValue) {
            const validDates = calculateValidEndDates(beginValue, 60);
            if (validDates.length > 0) {
                endInput.min = validDates[0];
            }
        } else {
            endInput.removeAttribute('min');
        }

        // إعادة التحقق من تاريخ النهاية
        validateEndDate();
    }

    // منع إرسال النموذج إذا كان التاريخ خاطئ
    form.addEventListener('submit', function(e) {
        if (!isEndDateValid()) {
            e.preventDefault();
            validateEndDate(); // عرض رسالة الخطأ
            endInput.focus(); // التركيز على الحقل الخاطئ
            return false;
        }
    });

    // ربط الأحداث
    beginInput.addEventListener('change', onBeginDateChange);
    endInput.addEventListener('change', validateEndDate);

    // التشغيل الأولي
    onBeginDateChange();
});
</script>

<style>
    /* Improve date input appearance */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }
</style>
@endsection
