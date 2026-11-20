<?php
/**
 * Charge les données dynamiques du portfolio
 * (depuis les fichiers JSON écrits par l'admin, ou fallback sur les fichiers PHP)
 */

$data_path     = __DIR__;
$settings_file = $data_path . '/settings.json';
$projects_file = $data_path . '/projects.json';

// ─── Settings ─────────────────────────────────────────────
$default_settings = [
    'name'          => 'Rodrigue Ekoko',
    'profession_fr' => 'Développeur Web & Logiciel',
    'profession_en' => 'Web & Software Developer',
    'email'         => 'votre.email@example.com',
    'phone'         => '+237 6XX XXX XXX',
    'whatsapp'      => '237600000000',
    'location_fr'   => 'Cameroun',
    'location_en'   => 'Cameroon',
    'linkedin'      => 'https://www.linkedin.com/in/votre-profil',
    'github'        => 'https://github.com/votre-username',
    'site_url'      => 'https://rodrigue-ekoko.com',
    'cv_filename'   => 'CV-Rodrigue-Ekoko.pdf',
    'profile_photo' => '',
    'about_fr'      => "Je suis Rodrigue Ekoko, développeur Web & Logiciel passionné par la conception de solutions informatiques. Mon objectif est de mettre la technologie au service des besoins réels des entreprises et des particuliers.\n\nJ'interviens dans la conception d'interfaces web modernes, le développement d'applications métier, la gestion de bases de données et la création de plateformes digitales.\n\nJ'accorde une importance particulière à la qualité du code, à l'expérience utilisateur, à la performance et à l'évolutivité des applications.",
    'about_en'      => "I am Rodrigue Ekoko, a Web & Software Developer passionate about designing IT solutions. My goal is to put technology at the service of real needs of businesses and individuals.\n\nI work on designing modern web interfaces, developing business applications, database management and creating digital platforms.\n\nI place particular importance on code quality, user experience, performance and application scalability.",
];

if (file_exists($settings_file)) {
    $json_settings = json_decode(file_get_contents($settings_file), true);
    $portfolio_settings = is_array($json_settings) ? array_merge($default_settings, $json_settings) : $default_settings;
} else {
    $portfolio_settings = $default_settings;
}

// ─── Projects ─────────────────────────────────────────────
if (file_exists($projects_file)) {
    $json_projects = json_decode(file_get_contents($projects_file), true);
    $projects = is_array($json_projects) ? $json_projects : [];
} else {
    // Fallback: charger les projets depuis projects.php
    require_once $data_path . '/projects.php';
}

// Expose as $settings globally
$settings = $portfolio_settings;
