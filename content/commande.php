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

// Validation de la disponibilité
if (!$vehicule || $vehicule['status'] !== 'Disponible') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Ce véhicule n'est plus disponible.</div></div>";
    exit();
}

// Validation stricte du type
if ($type !== 'Achat' && $type !== 'Location') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Type de transaction invalide.</div></div>";
    exit();
}

// Validation du formulaire (POST)
if (isset($_POST['confirmer_commande'])) {
    $commandeDAO = new CommandeDAO($cnx);

    // Récupération des options choisies (pour le moment, on ne fait que les lire,
    // il faudrait modifier PL/pgSQL si tu veux les stocker en base)
    $mode_retrait = $_POST['mode_retrait'] ?? 'Retrait en concession';
    $mode_paiement = $_POST['mode_paiement'] ?? 'Immediat';

    if ($type === 'Achat') {
        $montant = (float)$vehicule['prix_achat'];
        $success = $commandeDAO->creerCommande($_SESSION['id_client'], $id_vehicule, 'Achat', $montant);
    } elseif ($type === 'Location') {
        $jours = (int)$_POST['jours'];
        $montant = ((float)$vehicule['prix_location'] * $jours); // La caution est gérée à part sur place
        $date_debut = date('Y-m-d');
        $date_fin = date('Y-m-d', strtotime("+$jours days"));
        $success = $commandeDAO->creerCommande($_SESSION['id_client'], $id_vehicule, 'Location', $montant, $date_debut, $date_fin);
    }

    if ($success) {
        // Redirection PRG avec message Flash
        $_SESSION['flash_success'] = "<h4><i class='fa-solid fa-check'></i> Réservation confirmée !</h4><p>Merci pour votre confiance. <strong>Un e-mail contenant votre reçu a été envoyé à votre adresse pour confirmer votre réservation.</strong></p>";
        header("Location: index_.php?page=accueil.php");
        exit();
    }
}
?>

<!-- DÉBUT DE LA VUE (Style Sixt) -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">REVOIR VOTRE RÉSERVATION</h2>

        <div class="row g-4">
            <!-- COLONNE GAUCHE : Formulaire & Options -->
            <div class="col-lg-8">
                <form method="post" action="index_.php?page=commande.php&id_vehicule=<?= $id_vehicule ?>&type=<?= $type ?>" id="form-commande">

                    <!-- Section Conducteur -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">Qui va conduire ?</h4>
                            <div class="mb-3">
                                <label class="form-label text-muted">Adresse e-mail</label>
                                <!-- Affichage de l'email depuis la session -->
                                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($_SESSION['client']['email'] ?? 'alan.bombo@student.be') ?>" readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Prénom</label>
                                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($_SESSION['client']['prenom'] ?? 'Alan') ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nom de famille</label>
                                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($_SESSION['client']['nom'] ?? 'Bombo') ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Options Spécifiques (Achat ou Location) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">

                            <?php if ($type === 'Achat'): ?>
                                <h4 class="fw-bold mb-4">Options de réception</h4>
                                <div class="form-check mb-3 p-3 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="mode_retrait" id="retrait" value="Retrait en concession" checked>
                                    <label class="form-check-label ms-2 fw-bold" for="retrait">
                                        Retrait en concession (Gratuit)
                                    </label>
                                </div>
                                <div class="form-check p-3 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="mode_retrait" id="livraison" value="Livraison">
                                    <label class="form-check-label ms-2 fw-bold" for="livraison">
                                        Livraison à domicile (+250€)
                                    </label>
                                </div>

                            <?php elseif ($type === 'Location'): ?>
                                <h4 class="fw-bold mb-4">Détails de la location</h4>
                                <div class="alert alert-info border-0 bg-opacity-10 text-primary">
                                    <i class="fa-solid fa-circle-info"></i> Pour les locations, le retrait s'effectue obligatoirement en concession.
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Durée de la location (en jours)</label>
                                    <input type="number" name="jours" class="form-control form-control-lg w-25" value="1" min="1" max="30" required>
                                </div>

                                <h5 class="fw-bold mt-4 mb-3">Options de paiement</h5>
                                <div class="form-check mb-3 p-3 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="mode_paiement" id="pay_now" value="Immediat" checked>
                                    <label class="form-check-label ms-2 fw-bold" for="pay_now">
                                        Payer maintenant (Carte de crédit)
                                    </label>
                                </div>
                                <div class="form-check p-3 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="mode_paiement" id="pay_later" value="Sur place">
                                    <label class="form-check-label ms-2 fw-bold" for="pay_later">
                                        Payer sur place le jour du retrait
                                    </label>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </form>
            </div>

            <!-- COLONNE DROITE : Aperçu (Le Reçu) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body p-4 bg-light rounded-top">
                        <div class="d-flex align-items-center mb-3">
                            <img src="admin/assets/images/<?= htmlspecialchars($vehicule['image']) ?>" alt="Vehicule" class="img-fluid rounded me-3" style="width: 100px; object-fit: cover;">
                            <div>
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h5>
                                <small class="text-muted">Ou modèle similaire | <?= htmlspecialchars($type) ?></small>
                            </div>
                        </div>

                        <h6 class="fw-bold mt-4 mb-3">Ce qui est inclus</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 text-success"><i class="fa-solid fa-check me-2"></i> Excellent état garanti</li>
                            <?php if ($type === 'Location'): ?>
                                <li class="mb-2 text-success"><i class="fa-solid fa-check me-2"></i> Assistance dépannage 24/7</li>
                                <li class="mb-2 text-success"><i class="fa-solid fa-check me-2"></i> Kilométrage illimité</li>
                            <?php else: ?>
                                <li class="mb-2 text-success"><i class="fa-solid fa-check me-2"></i> Historique d'entretien vérifié</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <h3 class="fw-bold mb-0">Total</h3>
                            <h3 class="fw-bold mb-0 text-dark">
                                <?php if ($type === 'Achat'): ?>
                                    <?= number_format((float)$vehicule['prix_achat'], 2, ',', ' ') ?> €
                                <?php else: ?>
                                    <?= number_format((float)$vehicule['prix_location'], 2, ',', ' ') ?> € <span class="fs-6 text-muted fw-normal">/jour</span>
                                <?php endif; ?>
                            </h3>
                        </div>

                        <?php if ($type === 'Location'): ?>
                            <p class="text-muted small mb-4">
                                * Caution remboursable : Une empreinte bancaire de <strong><?= number_format((float)$vehicule['caution'], 2, ',', ' ') ?> €</strong> sera requise au comptoir lors du retrait.
                            </p>
                        <?php endif; ?>

                        <div class="alert alert-secondary small text-muted border-0">
                            Un email récapitulatif sera envoyé à votre adresse après validation de cette étape pour confirmer votre <?= strtolower($type) ?>.
                        </div>

                        <button type="submit" form="form-commande" name="confirmer_commande" class="btn btn-warning btn-lg w-100 fw-bold rounded-pill text-dark" style="background-color: #ff5f00; color: white !important; border: none;">
                            Demander la réservation maintenant
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>