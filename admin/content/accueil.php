<?php
// On récupère le nom mémorisé dans la session et on met la première lettre en majuscule
$nomAdmin = isset($_SESSION['nom_admin']) ? ucfirst($_SESSION['nom_admin']) : 'Admin';
?>
<div class="text-center mt-5">
    <h2><i class="fa-solid fa-gauge"></i> Tableau de bord Administrateur</h2>
    <p class="lead">Bienvenue dans l'espace de gestion de BonemRental <strong><?= $nomAdmin ?></strong>.</p>
</div>
