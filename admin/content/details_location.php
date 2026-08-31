<?php
// 1. Vérification de la présence de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: index_.php?page=locations.php");
    exit;
}

$id_commande = intval($_GET['id']);
$details = null;

// 2. Récupération des données de la location
try {
    $commandeDAO = new CommandeDAO($cnx);
    $details = $commandeDAO->getLocationById($id_commande);

    // Si la commande n'existe pas dans la base
    if (!$details) {
        // Redirection propre si l'ID n'existe pas
        header("Location: index_.php?page=locations.php");
        exit;
    }
} catch (Exception $e) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div></div>";
}

// On n'affiche le HTML que si on a bien trouvé les détails de la commande
if ($details) {
    ?>


    <div class="container-fluid mt-4">
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

                        <p><strong>Nom complet :</strong> <?= htmlspecialchars($details['prenom']) . ' ' . htmlspecialchars($details['nom']) ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($details['email']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($details['telephone']) ?></p>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($details['adresse']) ?></p>
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

                        <p><strong>Véhicule :</strong> <?= htmlspecialchars($details['marque']) . ' ' . htmlspecialchars($details['modele']) ?></p>
                        <p><strong>Date de départ :</strong> <?= date('d/m/Y', strtotime($details['date_debut'])) ?></p>
                        <p><strong>Date de retour :</strong> <?= date('d/m/Y', strtotime($details['date_fin'])) ?> </p>
                        <p class="fs-5 mt-3"><strong>Montant Total :</strong> <span class="text-primary"><?= number_format($details['montant_total'], 2, ',', ' ') ?> € </span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>