import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const main    = document.getElementById('main');
  const topbar  = document.getElementById('topbar'); // NEW: لازم تضيف id="topbar" في التوببار
  const burger  = document.getElementById('burger');
  const pinBtn  = document.getElementById('pinBtn');

  const isDesktop = () => window.innerWidth >= 992;

  // فتح/إغلاق على الجوال (لا نلمس الـtopbar هنا)
  burger?.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    if (!isDesktop()) {
      sidebar.classList.remove('mini'); // على الجوال نعرضه كامل
      main?.classList?.remove('mini');
      // لا تغييرات على topbar في الجوال
    }
  });

  // تثبيت/تصغير على الديسكتوب فقط + تحريك topbar معاه
  pinBtn?.addEventListener('click', () => {
    if (!isDesktop()) return;
    const isMini = sidebar.classList.toggle('mini');
    main?.classList?.toggle('mini', isMini);
    topbar?.classList?.toggle('mini', isMini); // NEW
  });

  // توسعة فورية عند المرور بالماوس على الديسكتوب فقط
  sidebar?.addEventListener('mouseenter', () => {
    if (isDesktop() && sidebar.classList.contains('mini')) {
      sidebar.classList.remove('mini');
      main?.classList?.remove('mini');
      topbar?.classList?.remove('mini'); // NEW
    }
  });

  sidebar?.addEventListener('mouseleave', () => {
    if (isDesktop()) {
      sidebar.classList.add('mini');
      main?.classList?.add('mini');
      topbar?.classList?.add('mini'); // NEW
    }
  });

  // إغلاق عند النقر خارج الشريط على الجوال
  document.addEventListener('click', (e) => {
    const clickInside = sidebar?.contains(e.target) || burger?.contains(e.target);
    if (!clickInside && !isDesktop() && sidebar?.classList.contains('show')) {
      sidebar.classList.remove('show');
    }
  });

  // تهيئة أولية + التكيّف عند تغيير المقاس
  const applyByViewport = () => {
    if (isDesktop()) {
      sidebar?.classList.remove('show');
      sidebar?.classList.add('mini');
      main?.classList.add('mini');
      topbar?.classList.add('mini'); // NEW: يبدأ متناسق مع mini
    } else {
      sidebar?.classList.remove('mini');
      main?.classList.remove('mini');
      topbar?.classList.remove('mini');
    }
  };

  applyByViewport();
  window.addEventListener('resize', applyByViewport);
});
