@extends('layouts.app')
@section('title','إضافة وحدة')

@section('content')
    {{-- Global errors (collective) --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4 overflow-visible">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-plus-circle me-2 text-primary"></i> إضافة وحدة جديدة
            </h3>
        </div>

        {{-- =========================================================
             FORM: Create Unit (alt layout)
             Maintenance Notes (EN):
             - Expects: $type_values, $status_values, $properties from controller.
             - Keep validation rules aligned with Unit::rules().
             - Use provided arrays for type/status (consistency with model).
             - Translations: __('unit.types.*'), __('unit.statuses.*') should exist.
             - To avoid dropdown clipping in RTL, wrapper uses overflow-visible.
           ========================================================= --}}
        <form action="{{ route('units.store') }}" method="POST" class="overflow-visible">
            @csrf

            <div class="row g-3 overflow-visible">
                {{-- =================== Relations First =================== --}}
                {{-- Property (full width, to reduce dropdown clipping issues) --}}
                <div class="col-12 overflow-visible">
                    <label for="property_id" class="form-label fw-semibold">العقار</label>
                    <select id="property_id" name="property_id"
                            dir="rtl"
                            style="text-align:right; text-align-last:right; unicode-bidi:plaintext;"
                            class="form-select @error('property_id') is-invalid @enderror"
                            required>
                        <option value="">-- اختر العقار --</option>
                        @foreach($properties as $property)
                            {{-- Display: Asset Name — City / Neighborhood --}}
                            <option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>
                                {{ $property->asset->name ?? '' }} — {{ $property->city }} / {{ $property->neighborhood }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">اختر العقار أولاً ثم أكمل بقية التفاصيل.</div>
                </div>

                {{-- =================== Basic Info =================== --}}
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">اسم الوحدة</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم الوحدة" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- =================== area =================== --}}
                <div class="col-md-6">
                    <label for="area" class="form-label fw-semibold">المساحة (م²)</label>
                    <input id="area" type="text" step="0.01" min="1" name="area" value="{{ old('area') }}"
                           class="form-control @error('area') is-invalid @enderror"
                           placeholder="أدخل المساحة" required>
                    @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- =================== Classification =================== --}}
                <div class="col-md-6">
                    <label for="type" class="form-label fw-semibold">نوع الوحدة</label>
                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">اختر النوع</option>
                        @foreach($type_values as $type)
                            <option value="{{ $type }}" @selected(old('type') == $type)>
                                {{ __('unit.types.' . $type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">الحالة</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="">اختر الحالة</option>
                        @foreach($status_values as $status)
                            <option value="{{ $status }}" @selected(old('status') == $status)>
                                {{ __('unit.statuses.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- =================== Description =================== --}}
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">الوصف</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="أدخل وصف الوحدة">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-4 text-end">
                <a href="{{ route('units.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>

        {{-- =================== Extra Maintenance Notes (EN) ===================
           - The property select is placed first and full-width to minimize native RTL dropdown clipping.
           - Wrapper uses .overflow-visible to prevent the dropdown from being cut by parent containers.
           - Keep numeric inputs as type="number" to match validation and mobile keyboards.
           - If a specific browser still clips the dropdown, consider moving the field near page top or using a custom select only for that case.
           ================================================================== --}}
    </div>
@endsection
