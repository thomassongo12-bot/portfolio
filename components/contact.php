<?php
// $settings est chargé par data/load.php via index.php
$s = $settings ?? [];
$email    = $s['email']    ?? 'votre.email@example.com';
$phone    = $s['phone']    ?? '+237 6XX XXX XXX';
$whatsapp = $s['whatsapp'] ?? '237600000000';
$linkedin = $s['linkedin'] ?? 'https://www.linkedin.com/in/votre-profil';
$github   = $s['github']   ?? 'https://github.com/votre-username';
?>

<section id="contact" class="section-padding" aria-label="Contact">
  <div class="container">

    <div class="section-header reveal">
      <span class="badge">Contact</span>
      <h2 data-i18n="contact.title">Vous avez un projet en tête&nbsp;?</h2>
      <p class="section-subtitle" data-i18n="contact.subtitle">Discutons de votre idée et construisons ensemble une solution adaptée à vos besoins.</p>
      <div class="section-line"></div>
    </div>

    <div class="contact-grid">

      <!-- Info column -->
      <div class="contact-info reveal-left">
        <h3>Restons en contact</h3>

        <div class="contact-info-items">

          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </div>
            <div class="contact-info-text">
              <strong data-i18n="contact.info_email">E-mail</strong>
              <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.4 2 2 0 0 1 3.6 2.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.1 6.1l1.27-.85a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </div>
            <div class="contact-info-text">
              <strong data-i18n="contact.info_phone">Téléphone / WhatsApp</strong>
              <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener noreferrer">
                <?= htmlspecialchars($phone) ?>
              </a>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
            </div>
            <div class="contact-info-text">
              <strong data-i18n="contact.info_location">Localisation</strong>
              <span data-i18n="contact.location">Cameroun</span>
            </div>
          </div>

        </div>

        <!-- Social links -->
        <div class="contact-socials">
          <a href="<?= htmlspecialchars($linkedin) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="LinkedIn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
              <rect x="2" y="9" width="4" height="12"/>
              <circle cx="4" cy="4" r="2"/>
            </svg>
          </a>
          <a href="<?= htmlspecialchars($github) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="GitHub">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
            </svg>
          </a>
          <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="WhatsApp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- Form column -->
      <div class="contact-form reveal-right">
        <form id="contactForm" novalidate>

          <div class="form-row">
            <div class="form-group">
              <label for="name" data-i18n="contact.name">Nom complet</label>
              <input type="text" id="name" name="name" autocomplete="name"
                     placeholder="Rodrigue Ekoko" required aria-required="true">
              <div class="form-error" id="nameError" role="alert"></div>
            </div>
            <div class="form-group">
              <label for="email" data-i18n="contact.email">Adresse e-mail</label>
              <input type="email" id="email" name="email" autocomplete="email"
                     placeholder="exemple@email.com" required aria-required="true">
              <div class="form-error" id="emailError" role="alert"></div>
            </div>
          </div>

          <div class="form-group">
            <label for="subject" data-i18n="contact.subject">Sujet</label>
            <input type="text" id="subject" name="subject"
                   placeholder="Votre sujet" required aria-required="true">
            <div class="form-error" id="subjectError" role="alert"></div>
          </div>

          <div class="form-group">
            <label for="message" data-i18n="contact.message">Message</label>
            <textarea id="message" name="message" rows="6"
                      placeholder="Décrivez votre projet ou votre demande…" required aria-required="true"></textarea>
            <div class="form-error" id="messageError" role="alert"></div>
          </div>

          <button type="submit" class="btn btn-primary form-submit" id="submitBtn">
            <span class="btn-text" style="display:flex;align-items:center;gap:8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
              <span data-i18n="contact.send">Envoyer le message</span>
            </span>
            <div class="spinner" aria-hidden="true"></div>
          </button>

          <div class="form-message" id="formMessage" role="status" aria-live="polite"></div>
        </form>
      </div>

    </div>
  </div>
</section>
