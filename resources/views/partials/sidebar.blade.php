<aside id="sidebar" class="sidebar mini">
    <div class="brand">
        <div class="brand-logo"><i class="fas fa-building"></i></div>
        <div class="brand-txt">
            <div class="brand-name">إدارة الأصول العقارية</div>
            <div class="brand-sub">لوحة التحكم</div>
        </div>
        <button id="pinBtn" class="pin-btn" title="توسيع/تصغير">
            <i class="fa-solid fa-thumbtack"></i>
        </button>
    </div>

    <nav class="nav-scroll">
        <a class="side-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
            <span class="pill"></span>
            <i class="ico fa-solid fa-home"></i><span class="txt">الرئيسية</span>
        </a>
        <a class="side-link" href="{{route('properties.index')}}"><span class="pill"></span><i class="ico fa-solid fa-building"></i><span class="txt">العقارات</span></a>
        <a class="side-link" href="{{route('units.index')}}"><span class="pill"></span><i class="ico fa-solid fa-door-open"></i><span class="txt">الوحدات</span></a>
        <a class="side-link" href="{{route('tenants.index')}}"><span class="pill"></span><i class="ico fa-solid fa-users"></i><span class="txt">المستأجرين</span></a>
        <a class="side-link" href="{{route('contracts.index')}}"><span class="pill"></span><i class="ico fa-solid fa-file-contract"></i><span class="txt">العقود</span></a>
        <a class="side-link" href="#"><span class="pill"></span><i class="ico fa-solid fa-money-bill-wave"></i><span class="txt">المدفوعات</span></a>
        <a class="side-link" href="#"><span class="pill"></span><i class="ico fa-solid fa-chart-line"></i><span class="txt">التقارير</span></a>
        <a class="side-link" href="#"><span class="pill"></span><i class="ico fa-solid fa-cog"></i><span class="txt">الإعدادات</span></a>
    </nav>

    <div class="side-footer">
        <div class="avatar">م</div>
        <div class="me-info">
            <div class="me-name">محمد أحمد</div>
            <div class="me-role">مدير النظام</div>
        </div>
    </div>
</aside>
