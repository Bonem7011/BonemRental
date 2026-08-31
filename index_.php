<?php
require_once "admin/src/php/utils/all_includes.php";
session_start();
ob_start(); // On active la mise en mémoire tampon de l'affichage


// 1. LA LISTE BLANCHE (Sécurisation obligatoire)
$pages_autorisees = [
        'accueil.php',
        'choix_transaction.php',
        'catalogue_location.php',
        'dates_location.php',
        'protections_location.php',
        'options_location.php',
        'recap_location.php',
        'traitement_final.php',
        'page_404.php',
        'connexion.php',
        'inscription.php',
        'logout_client.php',
        'commande.php',
        'confirmation.php'


];

// Gestion du système multipages (Routage dynamique)
if (!isset($_SESSION["page"])) {
    $_SESSION["page"] = "choix_transaction.php";
}

// 2. VÉRIFICATION DE LA VARIABLE GET
if (isset($_GET["page"])) {
    if (in_array($_GET["page"], $pages_autorisees)) {
        $_SESSION["page"] = $_GET["page"];
    } else {
        $_SESSION["page"] = "page_404.php"; // Redirection si la page n'est pas autorisée
    }
}


$path = "content/" . $_SESSION["page"];
?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BonemRental</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="admin/assets/css/style.css">

        <link rel="stylesheet" href="assets/css/sixt-style.css">
    </head>
    <body class="d-flex flex-column min-vh-100">

    <header>
        <?php require_once "admin/src/php/utils/public_menu.php"; ?>
    </header>

    <main class="container mt-4 flex-grow-1">
        <?php
        // Inclusion de la page demandée
        if (file_exists($path)) {
            include $path;
        } else {
            include "content/page_404.php";
        }
        ?>
    </main>

    <?php require_once "admin/src/php/utils/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php

    if ($_SESSION["page"] === "commande.php") {
    echo '<script src="assets/js/calculateur_achat.js"></script>';
    }

    if ($_SESSION["page"] === "dates_location.php") {
        echo '<script src="assets/js/calculateur_location.js"></script>';
    }
    if ($_SESSION["page"] === "options_location.php") {
        echo '<script src="assets/js/option.js"></script>';
    }

    if ($_SESSION["page"] === "protections_location.php") {
        echo '<script src="assets/js/protection.js"></script>';
    }
    ?>



    </body>
    </html>

<?php
ob_end_flush(); // On libère l'affichage mis en attente
?>