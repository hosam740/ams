document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const input = document.querySelector(btn.getAttribute('data-toggle-password'));
        if (!input) return;
        input.type = input.type === 'text' ? 'password' : 'text';
        const icon = btn.querySelector('i');
        if (icon){ icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash'); }
      });
    });
  });
  