# Guide de déploiement – Portfolio Rodrigue Ekoko

## ✅ Code pushé sur GitHub
**Repository :** https://github.com/thomassongo12-bot/portfolio.git

---

## Options d'hébergement PHP recommandées

| Hébergeur | Prix | PHP | MySQL | Domaine | Recommandé pour |
|---|---|---|---|---|---|
| **InfinityFree** | Gratuit | ✓ | ✓ | Sous-domaine | Test/démo |
| **000webhost** | Gratuit | ✓ | ✓ | Sous-domaine | Test/démo |
| **Hostinger** | ~2$/mois | ✓ 8.2 | ✓ | Inclus | Production |
| **o2switch** | ~6€/mois | ✓ | ✓ | Inclus | Production FR |
| **Infomaniak** | ~5€/mois | ✓ | ✓ | Inclus | Production FR/CH |

> Ce projet n'utilise **pas de base de données** — tout est en fichiers JSON.
> N'importe quel hébergeur PHP mutualisé fonctionne.

---

## Déploiement étape par étape

### 1. Télécharger le projet depuis GitHub

```bash
git clone https://github.com/thomassongo12-bot/portfolio.git
```

Ou télécharger le ZIP depuis GitHub → Code → Download ZIP.

### 2. Uploader sur l'hébergeur

Via **FTP** (FileZilla) ou le **gestionnaire de fichiers** du panneau de contrôle :

- Uploader **tout le contenu** du dossier `portfolio/` dans `public_html/` (ou `www/`)
- Vérifier que `index.php` est bien à la racine de `public_html/`

### 3. Créer les fichiers JSON manquants

Ces fichiers sont exclus du repo (`.gitignore`) car ils contiennent des données personnelles.
Créez-les manuellement sur l'hébergeur :

**`data/messages.json`** → contenu : `[]`

**`data/settings.json`** → copier depuis `data/settings.json.example` et remplir vos infos

**`data/auth.json`** → sera créé automatiquement par `setup.php`

### 4. Lancer setup.php

Allez sur : `https://votre-domaine.com/setup.php`

Ce script :
- Crée tous les fichiers JSON manquants
- Initialise le mot de passe admin (`Admin2024!`)
- Vérifie les permissions des dossiers
- Se verrouille automatiquement après

### 5. Se connecter à l'admin

URL : `https://votre-domaine.com/admin/`
- Login : `rodrigue`
- Mot de passe : `Admin2024!`

**→ Changez immédiatement le mot de passe** dans Admin → Mot de passe

### 6. Configurer

Dans l'admin :
1. **Paramètres** → email, téléphone, LinkedIn, GitHub, URL du site
2. **Profil** → textes de présentation
3. **Fichiers** → uploader votre photo de profil et votre CV
4. **Projets** → ajouter vos vraies images et URLs

### 7. Supprimer setup.php

Après configuration, supprimez `setup.php` depuis le gestionnaire de fichiers de votre hébergeur.

---

## Permissions requises (chmod)

```
data/           → 755
assets/images/  → 755
assets/files/   → 755
```

Si les uploads ne fonctionnent pas, passer à 775.

---

## Domaine personnalisé

Mettre à jour `index.php` ligne ~20 :
```php
$contact_api_url = $script_dir . '/api/contact.php';
```
Et dans `sitemap.xml`, remplacer `rodrigue-ekoko.com` par votre vrai domaine.

---

## Mettre à jour le site après modification

```bash
# Modifier les fichiers localement, puis :
git add .
git commit -m "update: description du changement"
git push origin master

# Sur l'hébergeur : re-télécharger les fichiers modifiés via FTP
# ou utiliser le déploiement automatique via GitHub Actions (voir ci-dessous)
```

---

## Déploiement automatique via GitHub Actions (optionnel)

Créez `.github/workflows/deploy.yml` sur votre repo pour déployer automatiquement à chaque push.
Nécessite les secrets GitHub : `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
