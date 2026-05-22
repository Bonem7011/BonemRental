<?php
// Vérifier que le client est connecté, sinon redirection
if (!isset($_SESSION['client'])) {
    header("Location: index_.php?page=connexion.php");
    exit();
}

$id_vehicule = isset($_GET['id_vehicule']) ? (int)$_GET['id_vehicule'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

$vehiculeDAO = new VehiculeDAO($cnx);
$vehicule = $vehiculeDAO->getVehiculeById($id_vehicule);

// Si le véhicule n'existe pas ou n'est plus disponible
if (!$vehicule || $vehicule['status'] !== 'Disponible') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Ce véhicule n'est plus disponible.</div></div>";
    exit();
}

// Validation du formulaire de confirmation
if (isset($_POST['confirmer_commande'])) {
    $commandeDAO = new CommandeDAO($cnx);

    if ($type === 'Achat') {
        $montant = (float)$vehicule['prix_achat'];
        $success = $commandeDAO->creerCommande($_SESSION['id_client'], $id_vehicule, 'Achat', $montant);
    } else {
        // Logique de location (simplifiée : 1 jour par défaut + caution)
        $jours = (int)$_POST['jours'];
        $montant = ((float)$vehicule['prix_location'] * $jours) + (float)$vehicule['caution'];
        $date_debut = date('Y-m-d');
        $date_fin = date('Y-m-d', strtotime("+$jours days"));

        $success = $commandeDAO->creerCommande($_SESSION['id_client'], $id_vehicule, 'Location', $montant, $date_debut, $date_fin);
    }

    if ($success) {
        echo "<div class='container mt-5'><div class='alert alert-success'><h4><i class='fa-solid fa-check'></i> Transaction validée !</h4><p>Merci pour votre confiance. Le véhicule est désormais à vous (ou réservé).</p><a href='index_.php' class='btn btn-success'>Retour à l'accueil</a></div></div>";
        exit();
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h4 class="mb-0">Récapitulatif de votre <?= htmlspecialchars($type) ?></h4>
                </div>
                <div class="card-body p-4 text-center">
                    <img src="admin/assets/images/<?= htmlspecialchars($vehicule['image']) ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>

                    <hr>

                    <form method="post" action="index_.php?page=commande.php&id_vehicule=<?= $id_vehicule ?>&type=<?= $type ?>">
                        <?php if ($type === 'Achat'): ?>
                            <div class="fs-4 mb-4">Montant total : <strong class="text-success"><?= number_format((float)$vehicule['prix_achat'], 2, ',', ' ') ?> €</strong></div>
                        <?php elseif ($type === 'Location'): ?>
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold">Durée de location (en jours)</label>
                                <input type="number" name="jours" class="form-control" value="1" min="1" max="30" required>
                                <small class="text-muted">Tarif journalier : <?= number_format((float)$vehicule['prix_location'], 2, ',', ' ') ?> € | Caution : <?= number_format((float)$vehicule['caution'], 2, ',', ' ') ?> €</small>
                            </div>
                        <?php endif; ?>

                        <button type="submit" name="confirmer_commande" class="btn btn-warning btn-lg w-100 fw-bold">
                            <i class="fa-solid fa-credit-card"></i> Confirmer le paiement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>