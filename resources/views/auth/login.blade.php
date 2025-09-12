@extends('layouts.auth')
@section('title','تسجيل الدخول')

@section('content')
  @if (session('status'))
    <div class="alert alert-success small">{{ session('status') }}</div>
  @endif

  {{-- أخطاء غير معروفة/غير مرتبطة بحقل معيّن + session('error') --}}
  @include('components.global-errors', ['skip' => ['email','password','remember']])

  <form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    <div class="mb-3">
      <label class="form-label">البريد الإلكتروني</label>
      <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        required autofocus>
      @error('email')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label class="form-label d-flex justify-content-between">
        <span>كلمة المرور</span>
        @if (Route::has('password.request'))
          <a class="small" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
        @endif
      </label>

      <div class="toggle-wrap">
        <input
          type="password"
          name="password"
          id="loginPassword"
          class="form-control @error('password') is-invalid @enderror"
          required>
        <button type="button" class="toggle-pass" data-toggle-password="#loginPassword" aria-label="إظهار/إخفاء">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>

      @error('password')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3 form-check">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">تذكرني</label>
    </div>

    <button type="submit" class="btn btn-brand w-100">تسجيل الدخول</button>
  </form>

  <div class="text-center mt-4 small">
    ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب</a>
  </div>
@endsection






<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4">Login</h2>

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

    <form method="POST" action="{{ route('login') }}" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Login</button>
    </form>

    <p class="mt-3">Don't have an account? 
        <a href="{{ route('show.register') }}">Register here</a>
    </p>
</body>
</html> -->