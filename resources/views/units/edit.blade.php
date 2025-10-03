@extends('layouts.app')
@section('title','تعديل الوحدة')

@section('content')
    {{-- Global errors --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-edit me-2 text-primary"></i> تعديل الوحدة
            </h3>
        </div>

        {{-- Form --}}
        <form action="{{ route('units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Unit Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم الوحدة</label>
                    <input type="text" name="name"
                           value="{{ old('name', $unit->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم الوحدة">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Type --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">النوع</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="">اختر النوع</option>
                        @foreach($type_values as $type)
                            <option value="{{ $type }}" {{ old('type', $unit->type) == $type ? 'selected' : '' }}>
                                {{ __('unit.types.' . $type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">الوصف</label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="أدخل وصفاً مختصراً للوحدة (حتى 1000 حرف)">{{ old('description', $unit->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Area --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">المساحة (م²)</label>
                    <input type="text" name="area" min="1" step="0.01"
                           value="{{ old('area', $unit->area) }}"
                           class="form-control @error('area') is-invalid @enderror"
                           placeholder="أدخل المساحة">
                    @error('area')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="">اختر الحالة</option>
                        @foreach($status_values as $status)
                            <option value="{{ $status }}" {{ old('status', $unit->status) == $status ? 'selected' : '' }}>
                                {{ __('unit.statuses.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('units.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    حفظ التغييرات
                </button>
            </div>

        </form>
    </div>
@endsection
