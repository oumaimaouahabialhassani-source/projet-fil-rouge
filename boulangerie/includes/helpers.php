<?php
/*
 * helpers.php - Fonctions PHP réutilisables
 * (PHP : fonctions + include/require)
 */

// Chemin de base du site (ex: /boulangerie/) — évite les liens cassés avec /admin
function baseUrl() {
    $dossier = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $dossier = preg_replace('#/admin$#', '', $dossier);
    if ($dossier === '/' || $dossier === '') {
        return '/';
    }
    return rtrim($dossier, '/') . '/';
}

// Retourne le chemin de l'image d'une catégorie (image uploadée ou image par défaut)
function categorieImage($cat) {
    if (!empty($cat['image']) && file_exists(__DIR__ . '/../images/' . $cat['image'])) {
        return 'images/' . $cat['image'];
    }

    // Images par défaut selon le nom (si aucune image uploadée)
    $nom = strtolower($cat['nom_categorie']);
    if ($nom == 'boulangerie') return 'image/Boulangerie.jpg';
    if ($nom == 'viennoiserie') return 'image/Viennoiserie.jpg';
    if ($nom == 'patisserie marocaine') return 'image/PatisserieMarocaine.jpg';
    if ($nom == 'patisserie moderne') return 'image/PatisserieModerne.jpg';
    if ($nom == 'tartes et gateaux') return 'image/TartesEtGateaux.jpg';

    return 'image/default.jpg';
}

// Retourne le chemin de l'image d'un produit (vide si pas d'image)
function produitImage($image) {
    if (!empty($image) && file_exists(__DIR__ . '/../images/' . $image)) {
        return 'images/' . $image;
    }
    return '';
}

// Enregistre un message à afficher sur la page suivante (PHP : sessions)
function enregistrerMessage($type, $message) {
    $_SESSION['message_type'] = $type;
    $_SESSION['message_texte'] = $message;
}

// Lit le message en session puis le supprime
function lireMessage() {
    if (!empty($_SESSION['message_texte'])) {
        $message = array(
            'type' => $_SESSION['message_type'],
            'texte' => $_SESSION['message_texte']
        );
        unset($_SESSION['message_type']);
        unset($_SESSION['message_texte']);
        return $message;
    }
    return null;
}
