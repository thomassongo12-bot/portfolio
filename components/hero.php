<section id="hero" aria-label="Section héro">
  <!-- Background decorations -->
  <div class="hero-bg" aria-hidden="true">
    <div class="hero-grid-lines"></div>
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
  </div>

  <div class="container">
    <div class="hero-inner">

      <!-- Left: Content -->
      <div class="hero-content reveal-left">
        <div class="hero-greeting" data-i18n="hero.greeting">BONJOUR, JE SUIS</div>

        <h1 class="hero-name" data-i18n="hero.name">Rodrigue Ekoko</h1>

        <p class="hero-profession" data-i18n="hero.profession">Développeur Web &amp; Logiciel</p>

        <p class="hero-tagline" data-i18n="hero.tagline">
          Je conçois et développe des solutions digitales modernes qui transforment les idées en expériences numériques performantes.
        </p>

        <p class="hero-description" data-i18n="hero.description">
          Passionné par le développement informatique, je crée des applications web, des plateformes SaaS et des logiciels adaptés aux besoins des entreprises.
        </p>

        <div class="hero-buttons">
          <a href="#projects" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="9" rx="1"/>
              <rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="13" width="9" height="9" rx="1"/>
            </svg>
            <span data-i18n="hero.btn_projects">Découvrir mes projets</span>
          </a>
          <a href="#contact" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
            <span data-i18n="hero.btn_contact">Me contacter</span>
          </a>
        </div>

        <div class="hero-badges">
          <span class="hero-badge">
            <span class="dot"></span>
            <span data-i18n="hero.available">Disponible pour des missions</span>
          </span>
          <span class="hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <span data-i18n="hero.specialized">Spécialisé Web &amp; Logiciel</span>
          </span>
        </div>
      </div>

      <!-- Right: Visual -->
      <div class="hero-visual reveal-right">

        <!-- Decorative code card – top left -->
        <div class="hero-code-card top-left" aria-hidden="true">
          <div class="code-line"><span class="code-keyword">const</span> <span class="code-fn">dev</span> = {</div>
          <div class="code-line">&nbsp; name: <span class="code-str">"Rodrigue"</span>,</div>
          <div class="code-line">&nbsp; role: <span class="code-str">"Fullstack"</span>,</div>
          <div class="code-line">};</div>
        </div>

        <!-- Photo -->
        <div class="hero-photo-container">
          <div class="hero-photo-ring" aria-hidden="true"></div>

          <?php
          $photo_jpg = 'assets/images/profile.jpg';
          $photo_png = 'assets/images/profile.png';
          $photo_webp= 'assets/images/profile.webp';
          if (file_exists($photo_jpg)): ?>
            <img src="<?= $photo_jpg ?>" alt="Rodrigue Ekoko – Développeur Web & Logiciel" class="hero-photo" loading="eager">
          <?php elseif (file_exists($photo_png)): ?>
            <img src="<?= $photo_png ?>" alt="Rodrigue Ekoko – Développeur Web & Logiciel" class="hero-photo" loading="eager">
          <?php elseif (file_exists($photo_webp)): ?>
            <img src="<?= $photo_webp ?>" alt="Rodrigue Ekoko – Développeur Web & Logiciel" class="hero-photo" loading="eager">
          <?php else: ?>
            <img src="assets/images/profile-placeholder.svg" alt="Rodrigue Ekoko – Développeur Web & Logiciel" class="hero-photo" loading="eager">
          <?php endif; ?>
        </div>

        <!-- Floating tags -->
        <div class="hero-floating-tag top-right" aria-hidden="true">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
          </svg>
          Fullstack Developer
        </div>

        <div class="hero-floating-tag bottom-left" aria-hidden="true">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
          </svg>
          React · NestJS · PHP
        </div>

        <!-- Decorative code card – bottom right -->
        <div class="hero-code-card bottom-right" aria-hidden="true">
          <div class="code-line"><span class="code-comment">// build solutions</span></div>
          <div class="code-line"><span class="code-fn">function</span> <span class="code-fn">solve</span>(idea) {</div>
          <div class="code-line">&nbsp; <span class="code-keyword">return</span> <span class="code-str">ship</span>(idea);</div>
          <div class="code-line">}</div>
        </div>

      </div>
    </div>
  </div>
</section>
