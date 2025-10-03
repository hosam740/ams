@extends('layouts.app')
@section('title','تعديل مستأجر')

@section('content')
    {{-- Global errors (collective) --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-edit me-2 text-primary"></i> تعديل مستأجر
            </h3>
        </div>

        {{-- =========================================================
             FORM: Edit Tenant
             Maintenance Notes (EN):
             - Expects $tenant passed from controller (edit()).
             - Keep fields aligned with Store/Update FormRequest rules.
             - Use old() with model fallback to preserve user input on errors.
           ========================================================= --}}
        <form action="{{ route('tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- First Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الاسم الأول</label>
                    <input type="text" name="first_name"
                           value="{{ old('first_name', $tenant->first_name) }}"
                           class="form-control @error('first_name') is-invalid @enderror"
                           placeholder="أدخل الاسم الأول">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم العائلة</label>
                    <input type="text" name="last_name"
                           value="{{ old('last_name', $tenant->last_name) }}"
                           class="form-control @error('last_name') is-invalid @enderror"
                           placeholder="أدخل اسم العائلة">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- National ID / Iqama --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">رقم الهوية الوطنية/الإقامة</label>
                    <input type="text" name="national_id"
                           value="{{ old('national_id', $tenant->national_id) }}"
                           class="form-control @error('national_id') is-invalid @enderror"
                           placeholder="أدخل رقم الهوية أو الإقامة">
                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone Number --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">رقم الهاتف</label>
                    <input type="tel" name="phone_number"
                           value="{{ old('phone_number', $tenant->phone_number) }}"
                           class="form-control @error('phone_number') is-invalid @enderror"
                           placeholder="05xxxxxxxx">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nationality --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الجنسية</label>
                    <input type="text" name="nationality"
                           value="{{ old('nationality', $tenant->nationality) }}"
                           class="form-control @error('nationality') is-invalid @enderror"
                           placeholder="أدخل الجنسية">
                    @error('nationality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Notes (optional) --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="أي ملاحظات تخص المستأجر">{{ old('notes', $tenant->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-4 text-end">
                <a href="{{ route('tenants.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right-to-bracket me-1"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
@endsection
