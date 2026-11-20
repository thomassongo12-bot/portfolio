<?php
/**
 * Rodrigue Ekoko – Portfolio Professionnel
 * index.php – Point d'entrée principal
 */

// Charger les données dynamiques (admin)
require_once __DIR__ . '/data/load.php';
// $settings et $projects sont maintenant disponibles

$defaultLang = 'fr';

$seo = [
    'fr' => [
        'title'       => 'Rodrigue Ekoko | Développeur Web & Logiciel',
        'description' => 'Découvrez le portfolio de Rodrigue Ekoko, développeur Web & Logiciel spécialisé dans la création d\'applications web, de logiciels et de solutions digitales.',
        'og_title'    => 'Rodrigue Ekoko | Développeur Web & Logiciel',
        'og_desc'     => 'Portfolio professionnel de Rodrigue Ekoko – Développeur Web & Logiciel basé au Cameroun.',
    ],
    'en' => [
        'title'       => 'Rodrigue Ekoko | Web & Software Developer',
        'description' => 'Discover the portfolio of Rodrigue Ekoko, a Web & Software Developer specialized in building web applications, software and digital solutions.',
        'og_title'    => 'Rodrigue Ekoko | Web & Software Developer',
        'og_desc'     => 'Professional portfolio of Rodrigue Ekoko – Web & Software Developer based in Cameroon.',
    ],
];

$meta = $seo[$defaultLang];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- SEO -->
  <title><?= htmlspecialchars($meta['title']) ?></title>
  <meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
  <meta name="author"      content="Rodrigue Ekoko">
  <meta name="robots"      content="index, follow">
  <link rel="canonical"    href="https://rodrigue-ekoko.com/">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="https://rodrigue-ekoko.com/">
  <meta property="og:title"       content="<?= htmlspecialchars($meta['og_title']) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta['og_desc']) ?>">
  <meta property="og:image"       content="https://rodrigue-ekoko.com/assets/images/og-image.jpg">
  <meta property="og:locale"      content="fr_FR">
  <meta property="og:site_name"   content="Rodrigue Ekoko Portfolio">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($meta['og_title']) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($meta['og_desc']) ?>">
  <meta name="twitter:image"       content="https://rodrigue-ekoko.com/assets/images/og-image.jpg">

  <!-- Favicon -->
  <link rel="icon"             type="image/svg+xml" href="assets/images/favicon.svg">
  <link rel="icon"             type="image/png"     href="assets/images/favicon.png" sizes="32x32">
  <link rel="apple-touch-icon"                      href="assets/images/apple-touch-icon.png">
  <meta name="theme-color" content="#2563EB">

  <!-- Google Fonts – Inter & Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="assets/css/style.css">

  <?php
  // URL de base calculée depuis le chemin réel du fichier (jamais fausse)
  $script_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
  // Si index.php est à la racine (/portfolio/index.php → /portfolio)
  // Si déployé à la racine (/index.php → vide → /)
  $contact_api_url = ($script_dir === '' || $script_dir === '.')
    ? '/api/contact.php'
    : $script_dir . '/api/contact.php';
  ?>
  <!-- Config JS injectée par PHP -->
  <script>
    window.PORTFOLIO_CONFIG = {
      contactApiUrl: '<?= htmlspecialchars($contact_api_url) ?>'
    };
  </script>

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Person",
    "name": "Rodrigue Ekoko",
    "jobTitle": "Développeur Web & Logiciel",
    "url": "https://rodrigue-ekoko.com",
    "sameAs": [
      "https://www.linkedin.com/in/votre-profil",
      "https://github.com/votre-username"
    ],
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "CM"
    },
    "knowsAbout": ["React", "TypeScript", "PHP", "NestJS", "PostgreSQL", "Node.js"]
  }
  </script>
</head>

<body>

  <!-- Navigation -->
  <?php include 'components/navbar.php'; ?>

  <main id="main-content">

    <!-- Hero -->
    <?php include 'components/hero.php'; ?>

    <!-- About -->
    <?php include 'components/about.php'; ?>

    <!-- Skills -->
    <?php include 'components/skills.php'; ?>

    <!-- Projects -->
    <?php include 'components/projects.php'; ?>

    <!-- Services -->
    <?php include 'components/services.php'; ?>

    <!-- Work Process -->
    <?php include 'components/process.php'; ?>

    <!-- Contact -->
    <?php include 'components/contact.php'; ?>

  </main>

  <!-- Footer -->
  <?php include 'components/footer.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/translations.js"></script>
  <script src="assets/js/main.js" defer></script>

</body>
</html>
