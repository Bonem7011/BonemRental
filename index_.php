<?php
// On inclut le moteur PHP
require_once "admin/src/php/utils/all_includes.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BonemRental - Accueil</title>
</head>
<body>
<h1>Bienvenue sur BonemRental</h1>
<?php
if (isset($cnx) && $cnx !== null) {
    echo "<p style='color: green;'>La connexion à la base de données PostgreSQL est réussie !</p>";
} else {
    echo "<p style='color: red;'>Échec de la connexion.</p>";
}
?>
</body>
</html>
