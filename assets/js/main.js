/* ========================================
   RODRIGUE EKOKO – Portfolio
   main.js
   ======================================== */

// ─── Language management ─────────────────────────────────
let currentLang = localStorage.getItem('portfolio_lang') || 'fr';

function setLang(lang) {
  if (!translations[lang]) return;
  currentLang = lang;
  localStorage.setItem('portfolio_lang', lang);
  applyTranslations();
  updateLangButtons();
}

function applyTranslations() {
  const t = translations[currentLang];
  if (!t) return;

  // data-i18n elements
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key   = el.getAttribute('data-i18n');
    const value = getNestedValue(t, key);
    if (value !== undefined) el.textContent = value;
  });

  // Project descriptions (data-desc-fr / data-desc-en)
  document.querySelectorAll('[data-desc-fr]').forEach(el => {
    el.textContent = currentLang === 'fr'
      ? el.getAttribute('data-desc-fr')
      : el.getAttribute('data-desc-en');
  });

  // Project type badges
  document.querySelectorAll('[data-project-type-fr]').forEach(el => {
    el.textContent = currentLang === 'fr'
      ? el.getAttribute('data-project-type-fr')
      : el.getAttribute('data-project-type-en');
  });

  // Modal types
  document.querySelectorAll('[data-type-fr]').forEach(el => {
    el.textContent = currentLang === 'fr'
      ? el.getAttribute('data-type-fr')
      : el.getAttribute('data-type-en');
  });

  // Update html lang attribute
  document.documentElement.lang = currentLang;
}

function getNestedValue(obj, path) {
  return path.split('.').reduce((acc, key) => acc && acc[key], obj);
}

function updateLangButtons() {
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === currentLang);
  });
}

// ─── Navbar scroll behavior ──────────────────────────────
const navbar = document.getElementById('navbar');

function handleNavbarScroll() {
  if (!navbar) return;
  if (window.scrollY > 20) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
}

// ─── Active nav link highlight ───────────────────────────
const sections   = document.querySelectorAll('section[id]');
const navLinks   = document.querySelectorAll('.nav-link');

function setActiveLink() {
  let current = '';
  sections.forEach(section => {
    const top = section.offsetTop - 100;
    if (window.scrollY >= top) current = section.id;
  });
  navLinks.forEach(link => {
    link.classList.remove('active');
    if (link.getAttribute('href') === '#' + current) link.classList.add('active');
  });
}

// ─── Scroll progress bar ─────────────────────────────────
const progressBar = document.getElementById('scrollProgress');

function updateProgress() {
  if (!progressBar) return;
  const total   = document.documentElement.scrollHeight - window.innerHeight;
  const pct     = total > 0 ? (window.scrollY / total) * 100 : 0;
  progressBar.style.width = pct + '%';
}

// ─── Back to top ─────────────────────────────────────────
const backToTop = document.getElementById('backToTop');

function handleBackToTop() {
  if (!backToTop) return;
  backToTop.classList.toggle('show', window.scrollY > 400);
}

// ─── Scroll reveal ───────────────────────────────────────
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

function initReveal() {
  document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => {
    revealObserver.observe(el);
  });
}

// ─── Hamburger / mobile menu ─────────────────────────────
const hamburger  = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

function toggleMobile() {
  if (!hamburger || !mobileMenu) return;
  const open = hamburger.classList.toggle('open');
  hamburger.setAttribute('aria-expanded', open);
  mobileMenu.classList.toggle('open', open);
  document.body.style.overflow = open ? 'hidden' : '';
}

function closeMobile() {
  if (!hamburger || !mobileMenu) return;
  hamburger.classList.remove('open');
  hamburger.setAttribute('aria-expanded', 'false');
  mobileMenu.classList.remove('open');
  document.body.style.overflow = '';
}

// ─── Smooth scroll + close mobile on nav click ───────────
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const href = anchor.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (!target) return;
      e.preventDefault();
      closeMobile();
      const offset = navbar ? navbar.offsetHeight : 72;
      const top    = target.getBoundingClientRect().top + window.pageYOffset - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}

// ─── Project modals ──────────────────────────────────────
function openModal(id) {
  const overlay = document.getElementById('modal-' + id);
  if (!overlay) return;
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  // Focus the close button for accessibility
  const closeBtn = overlay.querySelector('.modal-close');
  if (closeBtn) setTimeout(() => closeBtn.focus(), 100);
}

function closeModal(id) {
  const overlay = document.getElementById('modal-' + id);
  if (!overlay) return;
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}

function closeModalOnOverlay(event, id) {
  if (event.target === event.currentTarget) closeModal(id);
}

// Close modal on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(overlay => {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    });
    closeMobile();
  }
});

// ─── Contact form ────────────────────────────────────────
function initContactForm() {
  const form      = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const msgBox    = document.getElementById('formMessage');
  if (!form) return;

  function getT(key) {
    return getNestedValue(translations[currentLang], key) || '';
  }

  function showError(fieldId, errorId, message) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (field) field.classList.add('error');
    if (err)   { err.textContent = message; err.classList.add('show'); }
  }

  function clearError(fieldId, errorId) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (field) field.classList.remove('error');
    if (err)   { err.textContent = ''; err.classList.remove('show'); }
  }

  function clearAllErrors() {
    ['name','email','subject','message'].forEach(id => clearError(id, id + 'Error'));
  }

  // Live validation
  ['name','email','subject','message'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => clearError(id, id + 'Error'));
  });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    clearAllErrors();
    if (msgBox) { msgBox.className = 'form-message'; msgBox.textContent = ''; }

    const name    = document.getElementById('name').value.trim();
    const email   = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let valid = true;
    if (!name)             { showError('name',    'nameError',    getT('contact.validation_name'));    valid = false; }
    if (!emailRe.test(email)) { showError('email', 'emailError',  getT('contact.validation_email'));   valid = false; }
    if (!subject)          { showError('subject', 'subjectError', getT('contact.validation_subject')); valid = false; }
    if (!message)          { showError('message', 'messageError', getT('contact.validation_message')); valid = false; }
    if (!valid) return;

    // Show loading state
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // URL de l'API – injectée par PHP dans window.PORTFOLIO_CONFIG
    const apiBase = (window.PORTFOLIO_CONFIG && window.PORTFOLIO_CONFIG.contactApiUrl)
      ? window.PORTFOLIO_CONFIG.contactApiUrl
      : 'api/contact.php';

    try {
      const res = await fetch(apiBase, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ name, email, subject, message })
      });

      let data;
      try {
        data = await res.json();
      } catch {
        if (msgBox) { msgBox.className = 'form-message error'; msgBox.textContent = getT('contact.error'); }
        return;
      }

      if (data.success) {
        if (msgBox) { msgBox.className = 'form-message success'; msgBox.textContent = getT('contact.success'); }
        form.reset();
      } else {
        const errMsg = data.message || getT('contact.error');
        if (msgBox) { msgBox.className = 'form-message error'; msgBox.textContent = errMsg; }
      }
    } catch (networkErr) {
      if (msgBox) { msgBox.className = 'form-message error'; msgBox.textContent = getT('contact.error'); }
    } finally {
      submitBtn.classList.remove('loading');
      submitBtn.disabled = false;
    }
  });
}

// ─── Copyright year ──────────────────────────────────────
function setCopyrightYear() {
  const el = document.getElementById('copyrightYear');
  if (el) el.textContent = new Date().getFullYear();
}

// ─── Scroll event handler (throttled) ───────────────────
let scrollTicking = false;
function onScroll() {
  if (!scrollTicking) {
    requestAnimationFrame(() => {
      handleNavbarScroll();
      setActiveLink();
      updateProgress();
      handleBackToTop();
      scrollTicking = false;
    });
    scrollTicking = true;
  }
}

// ─── Lazy image fade-in ──────────────────────────────────
function initLazyImages() {
  const imgs = document.querySelectorAll('img[loading="lazy"]');
  imgs.forEach(img => {
    if (img.complete) {
      img.classList.add('loaded');
    } else {
      img.addEventListener('load', () => img.classList.add('loaded'));
    }
  });
}

// ─── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  // Language
  applyTranslations();
  updateLangButtons();

  // Events
  window.addEventListener('scroll', onScroll, { passive: true });
  handleNavbarScroll();
  setActiveLink();
  updateProgress();

  // Hamburger
  if (hamburger) hamburger.addEventListener('click', toggleMobile);

  // Smooth scroll
  initSmoothScroll();

  // Reveal on scroll
  initReveal();

  // Contact form
  initContactForm();

  // Footer year
  setCopyrightYear();

  // Lazy images
  initLazyImages();
});

// Expose modal functions globally
window.openModal         = openModal;
window.closeModal        = closeModal;
window.closeModalOnOverlay = closeModalOnOverlay;
window.setLang           = setLang;
