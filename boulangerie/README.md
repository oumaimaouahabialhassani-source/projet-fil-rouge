# 🥖 Boulangerie Ouahabi — Gestion de Boulangerie

## Structure du projet
```
/boulangerie
  ├── db.php          ← Connexion PDO MySQL
  ├── index.php       ← Page d'accueil (hero + about + catégories + produits)
  ├── produits.php    ← Catalogue complet avec filtres
  ├── ajouter.php     ← Formulaire ajout produit
  ├── modifier.php    ← Formulaire modification produit
  ├── supprimer.php   ← Confirmation suppression produit
  ├── setup.sql       ← Script SQL pour créer la DB et les tables
  ├── css/
  │   └── style.css   ← Styles complets (editorial/magazine chic)
  └── images/         ← Dossier pour les photos produits
```

## Installation rapide

1. **Créer la base de données** :
   ```bash
   mysql -u root -p < setup.sql
   ```

2. **Configurer la connexion** dans `db.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'boulangerie_ouahabi');
   define('DB_USER', 'root');
   define('DB_PASS', 'votre_mot_de_passe');
   ```

3. **Placer le dossier** dans `htdocs/` (XAMPP) ou `www/` (WAMP/Laragon)

4. **Ouvrir** `http://localhost/boulangerie/`

## Si vous avez déjà la base de données
Ajustez seulement `DB_NAME`, `DB_USER`, `DB_PASS` dans `db.php`.

## Design
- Police: Playfair Display (display) + DM Sans (body)
- Palette: crème / beige chaud / brun / blanc
- Style: éditorial / magazine artisanal
- Responsive mobile complet
