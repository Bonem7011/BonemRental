<?php
session_start();
ob_start(); // On active la mise en mémoire tampon de l'affichage
require_once "admin/src/php/utils/all_includes.php";

// Gestion du système multipages (Routage dynamique)
if (!isset($_SESSION["page"])) {
    $_SESSION["page"] = "accueil.php";
}
if (isset($_GET["page"])) {
    $_SESSION["page"] = $_GET["page"];
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
</body>
</html>

<?php
ob_end_flush(); //  On libère l'affichage mis en attente
?>