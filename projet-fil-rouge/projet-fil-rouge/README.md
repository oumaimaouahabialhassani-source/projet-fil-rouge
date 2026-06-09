# Boulangerie Ouahabi — Projet Fil Rouge

Site web vitrine et back-office pour la **Boulangerie Ouahabi**, boulangerie artisanale à Tanger (Maroc).  
Projet réalisé dans le cadre d'une formation (HTML, CSS, PHP, MySQL, JavaScript).

---

## Fonctionnalités

### Site public
- **Page d'accueil** : bannière hero, présentation, statistiques, carrousel de produits, catégories, témoignage
- **Catalogue produits** (`produits.php`) : grille de produits avec filtre par catégorie via l'URL (`?categorie=`)
- Navigation : Accueil, À propos, Produits, Catégories
- Aucun lien vers l'administration sur le site public (réservé aux administrateurs)

### Back-office (administration)
- **Connexion sécurisée** avec sessions PHP et mots de passe hashés (`password_verify`)
- **Tableau de bord** : statistiques (produits, catégories), derniers produits ajoutés
- **Gestion des produits** : liste, recherche, filtre par catégorie, ajout, modification, suppression
- **Gestion des catégories** : ajout, modification, suppression (si aucun produit lié), upload d'image
- **Déconnexion** → redirection vers la page de connexion

---

## Technologies utilisées

| Couche | Technologies |
|--------|----------------|
| Front-end | HTML5, CSS3 (Flexbox, Grid, variables CSS, media queries) |
| Back-end | PHP 8 (PDO, sessions, `include`, formulaires POST) |
| Base de données | MySQL / MariaDB |
| JavaScript | Carrousel produits sur la page d'accueil |
| Serveur local | XAMPP (Apache + MySQL) |

---

## Structure du projet

```
projet-fil-rouge/
├── boulangerie/                    # Application web
│   ├── admin/                      # Pages du back-office
│   │   ├── index.php               # Tableau de bord
│   │   ├── produits.php            # Liste des produits
│   │   ├── categories.php          # CRUD catégories
│   │   ├── layout-start.php        # Layout admin (menu latéral)
│   │   └── layout-end.php
│   ├── includes/
│   │   ├── site-header.php         # En-tête site public
│   │   ├── site-footer.php         # Pied de page public
│   │   ├── helpers.php             # Fonctions utilitaires
│   │   ├── admin-layout-start.php  # Layout admin (pages racine)
│   │   └── admin-layout-end.php
│   ├── css/
│   │   └── style.css               # Feuille de style unique
│   ├── image/                      # Images statiques (logo, hero, défauts)
│   ├── images/                     # Images uploadées (produits, catégories)
│   ├── index.php                   # Page d'accueil
│   ├── produits.php                # Catalogue public
│   ├── login.php                   # Connexion admin
│   ├── logout.php                  # Déconnexion
│   ├── ajouter.php                 # Ajouter un produit
│   ├── modifier.php                # Modifier un produit
│   ├── supprimer.php               # Supprimer un produit
│   ├── db.php                      # Connexion PDO à MySQL
│   └── auth.php                    # Sessions et protection des pages admin
├── boulangerie_ouahabi (1).sql     # Script de création de la base de données
└── README.md
```

---

## Installation (XAMPP)

### 1. Prérequis
- [XAMPP](https://www.apachefriends.org/) installé (Apache + MySQL)
- PHP 8.x et MariaDB/MySQL

### 2. Placer le projet
Copier le dossier `boulangerie` dans `C:\xampp\htdocs\` :

```
C:\xampp\htdocs\boulangerie\
```

Ou créer un lien symbolique (junction) depuis le dépôt :

```powershell
cmd /c mklink /J C:\xampp\htdocs\boulangerie C:\Users\DELL\Desktop\repos\projet-fil-rouge\boulangerie
```

### 3. Démarrer XAMPP
- Lancer **Apache** et **MySQL** depuis le panneau XAMPP

### 4. Créer la base de données
1. Ouvrir **phpMyAdmin** : http://localhost/phpmyadmin
2. Importer le fichier `boulangerie_ouahabi (1).sql`
3. La base `boulangerie_ouahabi` est créée avec les tables et données de démo

**Colonne image pour les catégories** (si l'upload d'images catégories est utilisé) :

```sql
ALTER TABLE categorie ADD COLUMN image VARCHAR(255) DEFAULT NULL;
```

### 5. Configuration base de données
Le fichier `boulangerie/db.php` est préconfiguré pour XAMPP :

| Paramètre | Valeur |
|-----------|--------|
| Hôte | `localhost` |
| Base | `boulangerie_ouahabi` |
| Utilisateur | `root` |
| Mot de passe | *(vide)* |

Modifier ces valeurs si votre environnement est différent.

---

## Accès à l'application

| Page | URL |
|------|-----|
| Site public (accueil) | http://localhost/boulangerie/ |
| Catalogue produits | http://localhost/boulangerie/produits.php |
| Connexion admin | http://localhost/boulangerie/login.php |
| Back-office | http://localhost/boulangerie/admin/ |

### Identifiants administrateur (démo)

| Login | Mot de passe |
|-------|----------------|
| `admin` | `admin123` |

> La déconnexion depuis le back-office redirige vers `login.php`.

---

## Base de données

### Tables

| Table | Description |
|-------|-------------|
| `categorie` | Familles de produits (Boulangerie, Viennoiserie, etc.) |
| `produit` | Produits : nom, prix, stock, image, catégorie |
| `utilisateur` | Comptes administrateurs (login + mot de passe hashé) |

### Relations
- Un produit appartient à une catégorie (`produit.categorie_id` → `categorie.id`)
- Suppression en cascade si une catégorie est supprimée (contrainte FK)

---

## Concepts PHP / SQL abordés

- `require_once`, `include` pour réutiliser header, footer, layout
- Requêtes **SELECT**, **INSERT**, **UPDATE**, **DELETE**
- **JOIN** entre `produit` et `categorie`
- **PDO** avec requêtes préparées (`prepare` / `execute`)
- **Sessions** pour l'authentification et les messages flash
- **$_GET** pour le filtre catégorie sur le catalogue
- **$_POST** pour les formulaires d'ajout / modification
- Upload de fichiers (`$_FILES`, `move_uploaded_file`)
- `password_verify` pour la vérification des mots de passe

---

## Auteur

Projet fil rouge — Boulangerie Ouahabi, Tanger.
