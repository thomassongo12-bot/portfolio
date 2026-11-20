<div class="scroll-progress" id="scrollProgress"></div>

<nav class="navbar" id="navbar" role="navigation" aria-label="Navigation principale">
  <div class="container">
    <div class="navbar-inner">

      <!-- Logo -->
      <a href="#hero" class="nav-logo" aria-label="Rodrigue Ekoko - Accueil">
        RODRIGUE<span>.</span>
      </a>

      <!-- Desktop nav -->
      <ul class="nav-links" role="list">
        <li><a href="#hero"     class="nav-link" data-i18n="nav.home">Accueil</a></li>
        <li><a href="#about"    class="nav-link" data-i18n="nav.about">À propos</a></li>
        <li><a href="#skills"   class="nav-link" data-i18n="nav.skills">Compétences</a></li>
        <li><a href="#projects" class="nav-link" data-i18n="nav.projects">Projets</a></li>
        <li><a href="#services" class="nav-link" data-i18n="nav.services">Services</a></li>
        <li><a href="#contact"  class="nav-link" data-i18n="nav.contact">Contact</a></li>
      </ul>

      <!-- Actions -->
      <div class="nav-actions">
        <!-- Language switcher -->
        <div class="lang-switcher" role="group" aria-label="Sélecteur de langue">
          <button class="lang-btn active" data-lang="fr" onclick="setLang('fr')" aria-label="Français">FR</button>
          <button class="lang-btn"        data-lang="en" onclick="setLang('en')" aria-label="English">EN</button>
        </div>

        <!-- CV Download -->
        <a href="assets/files/<?= htmlspecialchars($settings['cv_filename'] ?? 'CV-Rodrigue-Ekoko.pdf') ?>" download class="btn-cv" aria-label="Télécharger le CV">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          <span data-i18n="nav.download_cv">Télécharger mon CV</span>
        </a>
      </div>

      <!-- Hamburger -->
      <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
        <span></span><span></span><span></span>
      </button>

    </div>
  </div>
</nav>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Menu mobile">
  <a href="#hero"     class="nav-link" data-i18n="nav.home">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    Accueil
  </a>
  <a href="#about"    class="nav-link" data-i18n="nav.about">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    À propos
  </a>
  <a href="#skills"   class="nav-link" data-i18n="nav.skills">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
    Compétences
  </a>
  <a href="#projects" class="nav-link" data-i18n="nav.projects">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="9" rx="1"/><rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="13" width="9" height="9" rx="1"/></svg>
    Projets
  </a>
  <a href="#services" class="nav-link" data-i18n="nav.services">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M20 12h2M2 12h2M12 20v2M12 2v2"/></svg>
    Services
  </a>
  <a href="#contact"  class="nav-link" data-i18n="nav.contact">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    Contact
  </a>

  <div class="mobile-menu-footer">
    <div class="lang-switcher">
      <button class="lang-btn active" data-lang="fr" onclick="setLang('fr')">FR</button>
      <button class="lang-btn"        data-lang="en" onclick="setLang('en')">EN</button>
    </div>
    <a href="assets/files/<?= htmlspecialchars($settings['cv_filename'] ?? 'CV-Rodrigue-Ekoko.pdf') ?>" download class="btn-cv">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      <span data-i18n="nav.download_cv">Télécharger mon CV</span>
    </a>
  </div>
</div>
