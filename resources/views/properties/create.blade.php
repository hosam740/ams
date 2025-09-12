@extends('layouts.app')

@section('title', 'إضافة عقار جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">إضافة عقار جديد</h5>
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

        <form action="{{ route('properties.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">اسم العقار</label>
                <input type="text" name="name" id="name" 
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">الدولة</label>
                <input type="text" name="country" id="country" 
                       class="form-control @error('country') is-invalid @enderror"
                       value="{{ old('country') }}" required>
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">المدينة</label>
                <input type="text" name="city" id="city" 
                       class="form-control @error('city') is-invalid @enderror"
                       value="{{ old('city') }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="neighborhood" class="form-label">الحي</label>
                <input type="text" name="neighborhood" id="neighborhood" 
                       class="form-control @error('neighborhood') is-invalid @enderror"
                       value="{{ old('neighborhood') }}" required>
                @error('neighborhood') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="url_location" class="form-label">رابط الموقع (اختياري)</label>
                <input type="url" name="url_location" id="url_location" 
                       class="form-control @error('url_location') is-invalid @enderror"
                       value="{{ old('url_location') }}">
                @error('url_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="area" class="form-label">المساحة (م²)</label>
                <input type="number" name="area" id="area" min="1"
                       class="form-control @error('area') is-invalid @enderror"
                       value="{{ old('area') }}" required>
                @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
