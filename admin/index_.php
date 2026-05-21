<?php
session_start();
ob_start();
require_once "src/php/utils/all_includes.php";

// Gestion de la page courante (Routage dynamique admin)
if (!isset($_SESSION["page_admin"])) {
    $_SESSION["page_admin"] = "accueil.php";
}
if (isset($_GET["page"])) {
    $_SESSION["page_admin"] = $_GET["page"];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Admin - BonemRental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<?php
// SÉCURITÉ : Vérification de la session
if (!isset($_SESSION['admin'])) {
    $path = "content/login.php";
} else {
    // Si connecté, on inclut le vrai menu d'administration
    require_once "src/php/utils/admin_menu.php";
    $path = "content/" . $_SESSION["page_admin"];
}
?>

<main class="container mt-4 flex-grow-1">
    <?php
    if (file_exists($path)) {
        include $path;
    } else {
        include "content/page_404.php";
    }
    ?>
</main>

<?php require_once "src/php/utils/footer.php"; ?>

</body>
</html>
