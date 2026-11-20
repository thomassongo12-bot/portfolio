/* ========================================
   RODRIGUE EKOKO – Admin Panel JS
   ======================================== */

document.addEventListener('DOMContentLoaded', () => {

  // ─── Sidebar mobile toggle ─────────────────────────────
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  const menuBtn  = document.getElementById('menuBtn');
  const closeBtn = document.getElementById('sidebarToggle');

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
    document.body.style.overflow = '';
  }

  menuBtn?.addEventListener('click', openSidebar);
  closeBtn?.addEventListener('click', closeSidebar);
  overlay?.addEventListener('click', closeSidebar);

  // ─── Tab switcher ──────────────────────────────────────
  window.switchTab = function(lang, btn) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    // Deactivate all tab buttons
    btn.closest('.tab-switcher').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    // Show target
    const target = document.getElementById('tab-' + lang);
    if (target) target.classList.add('active');
    btn.classList.add('active');
  };

  // ─── Flash auto-dismiss ────────────────────────────────
  const flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .4s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 400);
    }, 4000);
  }

  // ─── Confirm delete helper ─────────────────────────────
  window.confirmDelete = function(id, name) {
    if (confirm('Supprimer le projet "' + name + '" ?\nCette action est irréversible.')) {
      document.getElementById('deleteId').value = id;
      document.getElementById('deleteForm').submit();
    }
  };

  // ─── Sortable (drag & drop) ────────────────────────────
  const projectList = document.getElementById('projectList');
  if (projectList) {
    // Simple drag & drop without external library
    let dragging = null;

    projectList.querySelectorAll('.project-row').forEach(row => {
      row.setAttribute('draggable', 'true');

      row.addEventListener('dragstart', e => {
        dragging = row;
        row.style.opacity = '.4';
        e.dataTransfer.effectAllowed = 'move';
      });

      row.addEventListener('dragend', () => {
        row.style.opacity = '1';
        dragging = null;
        // Send new order
        const order = [...projectList.querySelectorAll('.project-row')].map(r => r.dataset.id);
        const csrf  = document.querySelector('input[name="csrf_token"]')?.value || '';
        fetch(window.location.pathname, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'act=reorder&order=' + encodeURIComponent(JSON.stringify(order)) + '&csrf_token=' + encodeURIComponent(csrf)
        });
      });

      row.addEventListener('dragover', e => {
        e.preventDefault();
        if (dragging && dragging !== row) {
          const bounding = row.getBoundingClientRect();
          const offset   = bounding.y + bounding.height / 2;
          if (e.clientY < offset) {
            projectList.insertBefore(dragging, row);
          } else {
            projectList.insertBefore(dragging, row.nextSibling);
          }
        }
      });
    });
  }

  // ─── Upload zone click passthrough ────────────────────
  document.querySelectorAll('.upload-zone').forEach(zone => {
    zone.addEventListener('click', e => {
      if (e.target === zone || e.target.classList.contains('upload-content') || e.target.closest('.upload-content')) {
        zone.querySelector('input[type="file"]')?.click();
      }
    });
  });

  // ─── Active nav link ───────────────────────────────────
  const currentPath = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(link => {
    const href = link.getAttribute('href')?.split('/').pop();
    if (href === currentPath) link.classList.add('active');
  });

});
