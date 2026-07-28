<?php
// Récupération de l'ID depuis l'URL
$id_vehicule = $_GET['id'] ?? null;

if (!$id_vehicule) {
    echo "<div class='alert alert-danger'>Aucun véhicule sélectionné.</div>";
    exit;
}

// Instanciation du DAO
require_once 'admin/src/php/classes/VehiculeDAO.class.php';
$vehiculeDAO = new VehiculeDAO($cnx);
$vehicule = $vehiculeDAO->getVehiculeById($id_vehicule);

if (!$vehicule) {
    echo "<div class='alert alert-danger'>Véhicule introuvable.</div>";
    exit;
}

$prix_journalier = $vehicule['prix_location'];
?>

<!-- Conteneur principal qui stocke les données pour le JS -->
<div class="container my-5" id="calculateur-location" data-prix-base="<?= $prix_journalier ?>">
    <div class="row shadow-lg rounded-4 overflow-hidden bg-white">

        <!-- COLONNE GAUCHE : Détails -->
        <div class="col-lg-6 bg-dark text-white p-5 d-flex flex-column position-relative">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-uppercase mb-0"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h2>
                <p class="text-muted small">Catégorie Premium</p>
            </div>

            <div class="flex-grow-1 d-flex align-items-center justify-content-center my-4">
                <img src="admin/assets/images/<?= htmlspecialchars($vehicule['image']) ?>" alt="Voiture" class="img-fluid drop-shadow img-vehicule">
            </div>

            <div class="d-flex justify-content-center flex-wrap gap-3 small mt-auto">
                <span><i class="fas fa-users"></i> 5 Sièges</span>
                <span><i class="fas fa-suitcase"></i> 2 Valise(s)</span>
                <span><i class="fas fa-cog"></i> Automatique</span>
                <span><i class="fas fa-door-closed"></i> 5 Portes</span>
            </div>
        </div>

        <!-- COLONNE DROITE : Formulaire -->
        <div class="col-lg-6 p-5 d-flex flex-column">
            <form id="form-location" action="index_.php?page=options_location" method="POST">
                <input type="hidden" name="id_vehicule" value="<?= $vehicule['id_vehicule'] ?>">

                <h5 class="fw-bold mb-3">Période de location</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small">Date de départ</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Date de retour</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Options de paiement</h5>
                <div class="list-group mb-4">
                    <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
                        <div>
                            <input class="form-check-input me-2 option-calc" type="radio" name="option_paiement" value="0" checked>
                            <strong>Meilleur prix</strong>
                            <div class="text-muted small ms-4">Payez maintenant, annulez avec frais</div>
                        </div>
                        <span class="fw-bold small">Inclus</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
                        <div>
                            <input class="form-check-input me-2 option-calc" type="radio" name="option_paiement" value="14.66">
                            <strong>Restez flexible</strong>
                            <div class="text-muted small ms-4">Annulation gratuite jusqu'à la prise en charge</div>
                        </div>
                        <span class="fw-bold small">+ 14,66 € / jour</span>
                    </label>
                </div>

                <h5 class="fw-bold mb-3">Kilométrage</h5>
                <div class="list-group mb-4">
                    <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
                        <div>
                            <input class="form-check-input me-2 option-calc" type="radio" name="option_km" value="0" checked>
                            <strong>900 km</strong>
                        </div>
                        <span class="fw-bold small">Inclus</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
                        <div>
                            <input class="form-check-input me-2 option-calc" type="radio" name="option_km" value="3.85">
                            <strong>Kilomètres illimités</strong>
                        </div>
                        <span class="fw-bold small">+ 3,85 € / jour</span>
                    </label>
                </div>

                <div class="flex-grow-1"></div>

                <div class="d-flex justify-content-between align-items-end mt-4 pt-3 border-top">
                    <div>
                        <div class="fs-4 fw-bold">
                            <span id="affichage_prix_jour"><?= number_format($prix_journalier, 2, ',', ' ') ?> €</span> <span class="fs-6 fw-normal">/jour</span>
                        </div>
                        <div class="text-muted small">
                            <span id="affichage_prix_total">0,00 €</span> total (<span id="affichage_jours">0</span> jours)
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning text-white fw-bold px-5 py-2">Suivant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/calculateur_location.js"></script>
