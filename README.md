# Portfolio Professionnel – Rodrigue Ekoko

Portfolio développé en **PHP / HTML / CSS / JavaScript** pur, sans framework front-end, optimisé pour XAMPP et déployable sur tout hébergeur PHP.

---

## Structure du projet

```
portfolio/
├── index.php                  ← Point d'entrée principal
├── .htaccess                  ← Règles Apache (gzip, cache, sécurité)
├── sitemap.xml
├── robots.txt
├── README.md
│
├── components/
│   ├── navbar.php
│   ├── hero.php
│   ├── about.php
│   ├── skills.php
│   ├── projects.php
│   ├── services.php
│   ├── process.php
│   ├── contact.php
│   └── footer.php
│
├── data/
│   └── projects.php           ← FICHIER CENTRAL DES PROJETS
│
├── api/
│   └── contact.php            ← API d'envoi d'e-mail
│
└── assets/
    ├── css/style.css
    ├── js/
    │   ├── translations.js    ← Toutes les traductions FR/EN
    │   └── main.js
    ├── images/
    │   ├── favicon.svg
    │   ├── profile.jpg        ← VOTRE PHOTO (à ajouter)
    │   └── projects/          ← Images des projets (à ajouter)
    └── files/
        └── CV-Rodrigue-Ekoko.pdf  ← VOTRE CV (à ajouter)
```

---

## Lancement en local (XAMPP)

1. Placez le dossier `portfolio/` dans `C:\xampp\htdocs\`.
2. Démarrez **Apache** depuis le panneau XAMPP.
3. Ouvrez [http://localhost/portfolio/](http://localhost/portfolio/) dans votre navigateur.

---

## Personnalisation

### 1. Photo de profil
Ajoutez votre photo : `assets/images/profile.jpg`  
Dimensions recommandées : **500×500 px** minimum, format carré.

### 2. CV
Ajoutez votre CV : `assets/files/CV-Rodrigue-Ekoko.pdf`

### 3. Coordonnées (e-mail, téléphone, réseaux sociaux)
Modifiez les fichiers suivants :
- `components/contact.php` → e-mail, téléphone, LinkedIn, GitHub, WhatsApp
- `components/footer.php`  → liens réseaux sociaux
- `api/contact.php`        → `RECIPIENT_EMAIL` (ligne ~12)

### 4. Ajouter / modifier un projet
Éditez uniquement `data/projects.php`.  
Chaque projet suit cette structure :
```php
[
  "id"             => 7,
  "name"           => "MON PROJET",
  "type_fr"        => "Type en français",
  "type_en"        => "Type in English",
  "description_fr" => "Description en français.",
  "description_en" => "Description in English.",
  "technologies"   => ["React", "Node.js", "PostgreSQL"],
  "image"          => "assets/images/projects/mon-projet.png",
  "demo_url"       => "https://mon-projet.com",   // laisser "" si pas d'URL
  "github_url"     => "https://github.com/...",   // laisser "" si privé
  "color"          => "#2563EB"
],
```
> Les boutons « Voir le projet » et « Voir le code » n'apparaissent **que si** les URL sont renseignées.

### 5. Images des projets
Ajoutez vos captures d'écran dans `assets/images/projects/`.  
Format recommandé : **WebP ou PNG**, dimensions **800×450 px** (ratio 16:9).

### 6. Modifier les traductions
Éditez `assets/js/translations.js` pour ajuster n'importe quel texte en FR ou EN.

### 7. URL du site (SEO)
Dans `index.php`, remplacez `https://rodrigue-ekoko.com` par votre domaine réel.  
Faites de même dans `sitemap.xml` et `robots.txt`.

---

## Formulaire de contact

Le formulaire envoie via `api/contact.php` qui utilise la fonction PHP `mail()`.

**Prérequis :**
- Serveur avec `mail()` configuré (ou serveur SMTP via XAMPP en production).
- En local (XAMPP), le mail ne s'enverra pas sauf si un serveur SMTP est configuré.

Pour un envoi fiable en production, vous pouvez remplacer `mail()` dans `api/contact.php` par [PHPMailer](https://github.com/PHPMailer/PHPMailer) avec SMTP Gmail ou SendGrid.

---

## Déploiement

Le projet fonctionne sur tout hébergeur mutualisé avec PHP 8.0+ et Apache.  
Uploadez tous les fichiers à la racine de votre hébergement (ou sous-dossier).  
Vérifiez que le module `mod_rewrite` est activé sur votre hébergeur.

---

## Checklist avant mise en ligne

- [ ] Ajouter `assets/images/profile.jpg`
- [ ] Ajouter `assets/files/CV-Rodrigue-Ekoko.pdf`
- [ ] Remplacer `votre.email@example.com` dans `contact.php`, `footer.php` et `api/contact.php`
- [ ] Remplacer `+237 6XX XXX XXX` par le vrai numéro
- [ ] Remplacer les liens LinkedIn et GitHub par les vrais profils
- [ ] Mettre à jour l'URL du site dans `index.php`, `sitemap.xml` et `robots.txt`
- [ ] Ajouter les images des projets dans `assets/images/projects/`
- [ ] Renseigner les `demo_url` et `github_url` dans `data/projects.php` si disponibles
- [ ] Tester le formulaire de contact en production
