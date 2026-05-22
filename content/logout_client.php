<?php
session_start();
// On détruit uniquement les variables de session du client pour ne pas déconnecter l'admin si les deux sont ouverts
unset($_SESSION['client']);
unset($_SESSION['id_client']);
unset($_SESSION['prenom_client']);
header("Location: ../index_.php?page=accueil.php");
exit();
?>