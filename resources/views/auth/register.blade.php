@extends('layouts.auth')
@section('title','إنشاء حساب')

@section('content')
  <form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">الاسم الكامل</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}"
             class="form-control @error('name') is-invalid @enderror" required autofocus>
      @error('name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">البريد الإلكتروني</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}"
             class="form-control @error('email') is-invalid @enderror" required>
      @error('email')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="phone_number" class="form-label">رقم الجوال (اختياري)</label>
      <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}"
             class="form-control @error('phone_number') is-invalid @enderror" placeholder="05XXXXXXXX">
      @error('phone_number')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="regPassword" class="form-label">كلمة المرور</label>
      <div class="toggle-wrap">
        <input id="regPassword" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror" required>
        <button type="button" class="toggle-pass" data-toggle-password="#regPassword" aria-label="إظهار/إخفاء">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>
      @error('password')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
      <div class="form-text">٨ أحرف على الأقل.</div>
    </div>

    <div class="mb-3">
      <label for="regPasswordConfirm" class="form-label">تأكيد كلمة المرور</label>
      <div class="toggle-wrap">
        <input id="regPasswordConfirm" type="password" name="password_confirmation"
               class="form-control @error('password_confirmation') is-invalid @enderror" required>
        <button type="button" class="toggle-pass" data-toggle-password="#regPasswordConfirm" aria-label="إظهار/إخفاء">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>
      @error('password_confirmation')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-brand w-100">إنشاء حساب</button>
  </form>

  <div class="text-center mt-4 small">
    لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
  </div>
@endsection




<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4">Create Account</h2>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number (optional)</label>
            <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" class="form-control" placeholder="05XXXXXXXX">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="mt-3">Already have an account?
        <a href="{{ route('show.login') }}">Login here</a>
    </p>
</body>
</html> -->
