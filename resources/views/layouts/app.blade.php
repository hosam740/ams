<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','نظام إدارة الأصول العقارية')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- خطوط وأيقونات --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet"/>
    {{-- Bootstrap RTL --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"/>

    {{-- ملفات Vite --}}
    @vite(['resources/css/base.css','resources/css/app.css','resources/js/app.js'])
</head>
<body>
    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Topbar --}}
    @include('partials.topbar')

    {{-- Main --}}
    <main id="main" class="main mini">
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    {{-- Bootstrap JS (لو تحتاجه) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<!-- <!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'تطبيق العقارات')</title>

    {{-- Bootstrap RTL CSS (CDN) --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
          crossorigin="anonymous">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">تطبيق العقارات</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
                aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('properties.index') }}">العقارات</a></li>
            </ul>

            <div class="d-flex align-items-center">
                @auth
                    <span class="me-3">مرحباً، {{ auth()->user()->name }}</span>

                    {{-- زر إنشاء عقار --}}
                    <a href="{{ route('properties.create') }}" class="btn btn-primary me-2">
                        + إنشاء عقار
                    </a>

                    {{-- زر عرض العقارات --}}
                    <a href="{{ route('properties.index') }}" class="btn btn-info me-2">
                        عرض العقارات
                    </a>

                    {{-- زر إضافة مستأجر --}}
                    <a href="{{ route('tenants.create') }}" class="btn btn-success me-2">
                        + إضافة مستأجر
                    </a>

                    {{-- زر عرض المستأجرين --}}
                    <a href="{{ route('tenants.index') }}" class="btn btn-outline-dark me-2">
                        المستأجرون
                    </a>

                    {{-- زر إضافة عقد --}}
                    <a href="{{ route('contracts.create') }}" class="btn btn-success me-2">
                        + إضافة عقد
                    </a>

                    {{-- زر عرض العقود --}}
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-dark me-2">
                        العقود
                    </a>

                    {{-- لوحة التحكم (إن وُجدت) --}}
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary me-2">
                        لوحة التحكم
                    </a>

                    {{-- تسجيل خروج --}}
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">خروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">تسجيل الدخول</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">تسجيل</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    {{-- رسائل فلاش عامة --}}
    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

{{-- Bootstrap JS (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-s4r+2w03nfb3S2qFfC5qK3T7gQhQbTt+zqQ3aA9m0n8mJrj7q9T8b0E2zG+6x8yB"
        crossorigin="anonymous"></script>
</body>
</html> -->
