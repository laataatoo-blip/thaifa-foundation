async function mountContactModal() {
  const mount = document.getElementById('contact-modal-mount');
  if (!mount) return;

  try {
    const res = await fetch('components/contact-modal-component.html');
    const html = await res.text();
    mount.innerHTML = html;
  } catch (err) {
    console.error('Failed to load contact modal:', err);
  }
}

function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('hidden');
  el.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('hidden');
  el.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

document.addEventListener('click', (e) => {
  const closeBtn = e.target.closest('[data-close]');
  if (closeBtn) closeModal(closeBtn.getAttribute('data-close'));
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModal('contactModal');
});

// โหลด modal เมื่อหน้าเว็บโหลดเสร็จ
mountContactModal();
