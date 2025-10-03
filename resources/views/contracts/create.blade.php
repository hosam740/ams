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
                            {{ $tenant->first_name }} {{ $tenant->last_name }} — {{ $tenant->national_id }}
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
                    @foreach (
                        \App\Models\assets\Unit::query()
                            ->whereHas('property.asset', fn($q) => $q->where('manager_id', Auth::id()))
                            ->with(['property.asset'])
                            ->orderBy('id', 'desc')
                            ->get() as $unit
                    )
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
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

{{-- تحسين بسيط: اجعل تاريخ النهاية دائماً بعد البداية --}}
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
@endsection
