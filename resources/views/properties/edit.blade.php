@extends('layouts.app')
@section('title','تعديل العقار')

@section('content')
    {{-- Global errors --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-edit me-2 text-primary"></i> تعديل العقار
            </h3>
        </div>

        {{-- Form --}}
        <form action="{{ route('properties.update', $property) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Property Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم العقار</label>
                    <input type="text" name="name"
                           value="{{ old('name', optional($property->asset)->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم العقار">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الدولة</label>
                    <input type="text" name="country"
                           value="{{ old('country', $property->country) }}"
                           class="form-control @error('country') is-invalid @enderror"
                           placeholder="أدخل الدولة">
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- City --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">المدينة</label>
                    <input type="text" name="city"
                           value="{{ old('city', $property->city) }}"
                           class="form-control @error('city') is-invalid @enderror"
                           placeholder="أدخل المدينة">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Neighborhood --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الحي</label>
                    <input type="text" name="neighborhood"
                           value="{{ old('neighborhood', $property->neighborhood) }}"
                           class="form-control @error('neighborhood') is-invalid @enderror"
                           placeholder="أدخل الحي">
                    @error('neighborhood')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Area (text input) --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">المساحة (م²)</label>
                    <input type="text" name="area"
                           value="{{ old('area', $property->area) }}"
                           class="form-control @error('area') is-invalid @enderror"
                           placeholder="أدخل المساحة">
                    @error('area')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Location URL --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">رابط الموقع (خريطة)</label>
                    <input type="url" name="url_location"
                           value="{{ old('url_location', $property->url_location) }}"
                           class="form-control @error('url_location') is-invalid @enderror"
                           placeholder="https://maps.google.com/...">
                    @error('url_location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                     حفظ التغييرات
                </button>
            </div>

        </form>
    </div>
@endsection
