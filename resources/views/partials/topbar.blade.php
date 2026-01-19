<header id="topbar" class="topbar">
    <div class="d-flex align-items-center gap-2">
        <button id="burger" class="burger d-inline d-lg-none" aria-label="فتح القائمة">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div class="d-flex align-items-center gap-3">
        @auth
            <div class="dropdown">
                <button class="avatar btn p-0 border-0" data-bs-toggle="dropdown" aria-expanded="false" title="{{ Auth::user()->name }}">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li class="px-3 py-2">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket ms-2"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth

        @guest
            <a href="{{ route('show.login') }}" class="btn btn-sm btn-outline-primary">تسجيل الدخول</a>
        @endguest
    </div>
</header>
