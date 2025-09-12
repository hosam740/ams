<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','تسجيل الدخول')</title>

  {{-- Bootstrap RTL + أيقونات + خط --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet"/>

  {{-- ملفات Vite --}}
  @vite(['resources/css/base.css','resources/css/auth.css','resources/js/auth.js'])
</head>
<body class="auth-bg preload">
  <div class="auth-wrapper">
    <div class="auth-card card border-0 shadow-lg">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="auth-logo mb-2"><i class="fa-solid fa-building"></i></div>
          <h1 class="h5 fw-bold mb-0">إدارة الأصول العقارية</h1>
          <div class="text-muted small">مرحبًا بك</div>
        </div>

        @yield('content')
      </div>
    </div>
  </div>
</body>
</html>
