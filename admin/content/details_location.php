<?php
// 1. Vérification de la présence de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Erreur : Aucun identifiant de commande fourni.</div></div>";
    exit;
}

$id_commande = intval($_GET['id']);

// 2. Récupération des données de la location

try {
    // 1. On crée l'objet CommandeDAO (adapte $cnx selon le nom de ta variable de connexion)
    $commandeDAO = new CommandeDAO($cnx);

    // 2. Maintenant on peut l'utiliser ! (La ligne rouge va disparaître)
    $details = $commandeDAO->getLocationById($id_commande);

    // Si la commande n'existe pas dans la base
    if (!$details) {
        echo "<div class='container mt-4'><div class='alert alert-warning'>Commande introuvable.</div></div>";
        exit;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
    exit;
}


?>

<div class="container-fluid mt-4" style="font-family: Arial, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Détails de la Commande #<?= $id_commande ?></h2>
        <a href="index_.php?page=locations.php" class="btn btn-secondary">Retour aux locations</a>
    </div>

    <div class="row">
        <!-- Colonne Informations Client -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-user"></i> Informations Client
                </div>
                <div class="card-body">
                    <p><strong>Nom complet :</strong> <?= $details['prenom'] . ' ' . $details['nom'] ?></p>
                    <p><strong>Email :</strong> <?= $details['email'] ?></p>
                    <p><strong>Téléphone :</strong> <?= $details['telephone'] ?></p>
                    <p><strong>Adresse :</strong> <?= $details['adresse'] ?></p>
                </div>
            </div>
        </div>

        <!-- Colonne Informations Véhicule & Location -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-car"></i> Détails du Véhicule
                </div>
                <div class="card-body">
                    <p><strong>Véhicule :</strong> <?= $details['marque'] . ' ' . $details['modele'] ?></p>
                    <p><strong>Date de départ :</strong> <?= date('d/m/Y', strtotime($details['date_debut'])) ?></p>
                    <p><strong>Date de retour :</strong> <?= date('d/m/Y', strtotime($details['date_fin'])) ?> </p>
                    <p class="fs-5 mt-3"><strong>Montant Total :</strong> <span class="text-primary"><?= number_format($details['montant_total'], 2, ',', ' ') ?> € </span></p>
                </div>
            </div>
        </div>
    </div>
</div>