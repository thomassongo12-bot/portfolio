<?php
$skill_categories = [
  [
    "key"   => "frontend",
    "i18n"  => "skills.frontend",
    "label" => "Frontend",
    "icon"  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
    "skills"=> [
      ["name" => "HTML5",      "icon" => "🌐"],
      ["name" => "CSS3",       "icon" => "🎨"],
      ["name" => "JavaScript", "icon" => "⚡"],
      ["name" => "TypeScript", "icon" => "🔷"],
      ["name" => "React",      "icon" => "⚛️"],
    ]
  ],
  [
    "key"   => "backend",
    "i18n"  => "skills.backend",
    "label" => "Backend",
    "icon"  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>',
    "skills"=> [
      ["name" => "PHP",     "icon" => "🐘"],
      ["name" => "Node.js", "icon" => "🟢"],
      ["name" => "NestJS",  "icon" => "🔴"],
    ]
  ],
  [
    "key"   => "database",
    "i18n"  => "skills.database",
    "label" => "Bases de données",
    "icon"  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
    "skills"=> [
      ["name" => "MySQL",      "icon" => "🐬"],
      ["name" => "PostgreSQL", "icon" => "🐘"],
      ["name" => "SQL Server", "icon" => "🗄️"],
    ]
  ],
  [
    "key"   => "tools",
    "i18n"  => "skills.tools",
    "label" => "Outils & Environnement",
    "icon"  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    "skills"=> [
      ["name" => "Git",             "icon" => "🔀"],
      ["name" => "GitHub",          "icon" => "🐙"],
      ["name" => "Visual Studio",   "icon" => "💜"],
      ["name" => "VS Code",         "icon" => "💙"],
      ["name" => "Vite",            "icon" => "⚡"],
      ["name" => "Prisma",          "icon" => "🔺"],
    ]
  ],
  [
    "key"   => "cms",
    "i18n"  => "skills.cms",
    "label" => "CMS",
    "icon"  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
    "skills"=> [
      ["name" => "WordPress", "icon" => "📝"],
    ]
  ],
];
?>

<section id="skills" class="section-padding" aria-label="Compétences">
  <div class="container">

    <div class="section-header reveal">
      <span class="badge">Skills</span>
      <h2 data-i18n="skills.title">Mes Compétences</h2>
      <p class="section-subtitle" data-i18n="skills.subtitle">Technologies &amp; outils que j'utilise</p>
      <div class="section-line"></div>
    </div>

    <div class="skills-grid">
      <?php foreach ($skill_categories as $i => $cat): ?>
        <div class="skill-category reveal" style="transition-delay: <?= $i * 0.1 ?>s;">
          <div class="skill-cat-header">
            <div class="skill-cat-icon" aria-hidden="true"><?= $cat['icon'] ?></div>
            <span class="skill-cat-title" data-i18n="<?= $cat['i18n'] ?>"><?= htmlspecialchars($cat['label']) ?></span>
          </div>
          <div class="skill-tags">
            <?php foreach ($cat['skills'] as $skill): ?>
              <span class="skill-tag">
                <span aria-hidden="true"><?= $skill['icon'] ?></span>
                <?= htmlspecialchars($skill['name']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
