@extends('layouts.app')

@section('title', 'إضافة مستأجر جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">إضافة مستأجر جديد</h5>
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

        <form action="{{ route('tenants.store') }}" method="POST">
            @csrf

            {{-- الاسم الأول --}}
            <div class="mb-3">
                <label for="first_name" class="form-label">الاسم الأول</label>
                <input type="text" name="first_name" id="first_name"
                       class="form-control @error('first_name') is-invalid @enderror"
                       value="{{ old('first_name') }}" required>
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- اسم العائلة --}}
            <div class="mb-3">
                <label for="last_name" class="form-label">اسم العائلة</label>
                <input type="text" name="last_name" id="last_name"
                       class="form-control @error('last_name') is-invalid @enderror"
                       value="{{ old('last_name') }}" required>
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- رقم الهوية الوطنية / الإقامة --}}
            <div class="mb-3">
                <label for="national_id" class="form-label">رقم الهوية الوطنية/الإقامة</label>
                <input type="text" name="national_id" id="national_id"
                       class="form-control @error('national_id') is-invalid @enderror"
                       value="{{ old('national_id') }}" required>
                @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- رقم الهاتف --}}
            <div class="mb-3">
                <label for="phone_number" class="form-label">رقم الهاتف</label>
                <input type="text" name="phone_number" id="phone_number"
                       class="form-control @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number') }}" required>
                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- الجنسية --}}
            <div class="mb-3">
                <label for="nationality" class="form-label">الجنسية</label>
                <input type="text" name="nationality" id="nationality"
                       class="form-control @error('nationality') is-invalid @enderror"
                       value="{{ old('nationality') }}" required>
                @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('tenants.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
