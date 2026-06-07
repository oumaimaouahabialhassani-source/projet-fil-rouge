# 🥖 Boulangerie Ouahabi — Gestion de Boulangerie

Projet de fin de formation (PFE). Petit site de gestion de catalogue de boulangerie
développé en **HTML / CSS / JavaScript / PHP / MySQL**.

## Fonctionnalités
- Page d'accueil (présentation, catégories, meilleurs produits)
- Catalogue des produits avec filtre par catégorie
- **CRUD** complet : ajouter, modifier, supprimer un produit (avec image)
- **Connexion admin** (sessions PHP) : seul l'admin peut gérer le catalogue

## Structure du projet
```
/boulangerie
  ├── db.php          ← Connexion PDO à MySQL
  ├── auth.php        ← Gestion de la session (connexion admin)
  ├── login.php       ← Page de connexion admin
  ├── logout.php      ← Déconnexion
  ├── index.php       ← Page d'accueil
  ├── produits.php    ← Catalogue complet avec filtres
  ├── ajouter.php     ← Formulaire ajout produit (admin)
  ├── modifier.php    ← Formulaire modification produit (admin)
  ├── supprimer.php   ← Confirmation suppression produit (admin)
  ├── setup.sql       ← Script SQL : base, tables, données et compte admin
  ├── css/
  │   └── style.css   ← Styles du site
  └── images/         ← Dossier pour les photos des produits
```

## Installation

1. **Créer la base de données** (avec phpMyAdmin → Importer, ou en ligne de commande) :
   ```bash
   mysql -u root -p < setup.sql
   ```

2. **Configurer la connexion** dans `db.php` si besoin :
   ```php
   $host   = "localhost";
   $dbname = "boulangerie_ouahabi";
   $user   = "root";
   $pass   = "";
   ```

3. **Placer le dossier** dans `htdocs/` (XAMPP) ou `www/` (WAMP/Laragon).

4. **Ouvrir** `http://localhost/boulangerie/`

## Connexion administrateur
Pour ajouter / modifier / supprimer des produits, il faut se connecter :

| Login   | Mot de passe |
|---------|--------------|
| `admin` | `admin123`   |

Le mot de passe est stocké haché dans la base (`password_hash`) et vérifié avec
`password_verify`.

## Technologies utilisées
- **HTML5** (structure des pages, formulaires)
- **CSS3** (mise en page, responsive avec media queries)
- **JavaScript** (aperçu de l'image avant l'envoi)
- **PHP** (formulaires, superglobales, sessions, PDO)
- **MySQL** (base de données, jointures, CRUD)
