<?php
// 1. Initialisation du tunnel de location dans la session
if (!isset($_SESSION['panier_location'])) {
    $_SESSION['panier_location'] = [
        'id_vehicule' => null,
        'date_debut' => null,
        'date_fin' => null,
        'options' => [],
        'total' => 0
    ];
}

// 2. Instanciation du DAO (l'Autoloader s'occupe de l'inclusion)
// On lui passe $cnx, qui a été créé dans all_includes.php
$vehiculeDAO = new VehiculeDAO($cnx);

// 3. Récupération des véhicules
$vehicules_location = $vehiculeDAO->getVehiculesLocation();
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Sélectionnez votre véhicule de location</h2>
            <p class="text-muted">Kilométrage illimité et assurance de base incluse sur tous nos modèles.</p>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($vehicules_location)): ?>
            <?php foreach ($vehicules_location as $vehicule): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm vehicule-card">
                        <!-- Image du véhicule -->
                        <img src="admin/assets/images/<?= htmlspecialchars($vehicule['image']) ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>">

                        <div class="card-body d-flex flex-column">
                            <!-- Titre -->
                            <h5 class="card-title fw-bold mb-1">
                                <?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>
                            </h5>

                            <!-- Espace flexible pour le bas de la carte -->
                            <div class="mt-auto">
                                <hr class="text-muted opacity-25">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fs-4 fw-bold text-dark"><?= htmlspecialchars($vehicule['prix_location']) ?> €</span>
                                        <span class="text-muted small">/ jour</span>
                                    </div>
                                    <!-- Lien vers le traitement du choix -->
                                    <a href="index_.php?page=dates_location.php&action=choisir_vehicule&id=<?= $vehicule['id_vehicule'] ?>" class="btn btn-action-sixt">
                                        Sélectionner
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun véhicule n'est actuellement disponible à la location.</div>
            </div>
        <?php endif; ?>
    </div>
</div>