@extends('layouts.app')

@section('title', 'إضافة وحدة جديدة')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">إضافة وحدة جديدة</h5>
    </div>
    <div class="card-body">

        {{-- عرض الأخطاء --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('units.store') }}" method="POST">
            @csrf

            {{-- اسم الوحدة --}}
            <div class="mb-3">
                <label for="name" class="form-label">اسم الوحدة</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            {{-- نوع الوحدة --}}
            <div class="mb-3">
                <label for="type" class="form-label">نوع الوحدة</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">-- اختر النوع --</option>
                    @foreach (\App\Models\assets\Unit::getTypeValues() as $value)
                        <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                            {{ ucfirst($value) }}
                        </option>
                    @endforeach
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- الوصف --}}
            <div class="mb-3">
                <label for="description" class="form-label">الوصف</label>
                <textarea name="description" id="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror"
                          required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- المساحة --}}
            <div class="mb-3">
                <label for="area" class="form-label">المساحة (م²)</label>
                <input type="number" name="area" id="area" min="1"
                       class="form-control @error('area') is-invalid @enderror"
                       value="{{ old('area') }}" required>
                @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- الحالة --}}
            <div class="mb-3">
                <label for="status" class="form-label">الحالة</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">-- اختر الحالة --</option>
                    @foreach (\App\Models\assets\Unit::getStatusValues() as $value)
                        <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                            {{ ucfirst($value) }}
                        </option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- العقار المرتبط --}}
            <div class="mb-3">
                <label for="property_id" class="form-label">العقار</label>
                <select name="property_id" id="property_id" class="form-select @error('property_id') is-invalid @enderror" required>
                    <option value="">-- اختر العقار --</option>
                    @foreach (\App\Models\assets\Property::with('asset')->whereHas('asset', fn($q) => $q->where('manager_id', Auth::id()))->get() as $property)
                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->city }} - {{ $property->neighborhood }} ({{ $property->asset->name }})
                        </option>
                    @endforeach
                </select>
                @error('property_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('units.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
