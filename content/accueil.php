<?php
// On instancie le DAO pour récupérer les véhicules
$vehiculeDAO = new VehiculeDAO($cnx);
$listeVehicules = $vehiculeDAO->getVehicules();
?>

<div class="text-center py-5">
    <h1 class="display-4 fw-bold"><i class="fa-solid fa-car-side"></i> Bienvenue sur BonemRental</h1>
    <p class="lead mt-3">La plateforme de référence pour l'achat et la location de véhicules d'exception.</p>
    <hr class="my-4 w-50 mx-auto">
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">

    <?php if (empty($listeVehicules)): ?>
        <div class="col-12 text-center">
            <div class="alert alert-light border shadow-sm py-4">
                <i class="fa-solid fa-hourglass-empty fa-2x text-muted mb-3"></i>
                <p class="mb-0 text-muted">Notre catalogue est en cours de réapprovisionnement. Revenez très vite !</p>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($listeVehicules as $v): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden">

                    <div class="position-absolute top-0 end-0 m-3 z-3">
                        <span class="badge rounded-pill <?= $v['status'] == 'Disponible' ? 'bg-success' : 'bg-warning text-dark' ?> shadow">
                            <?= htmlspecialchars($v['status']) ?>
                        </span>
                    </div>

                    <img src="admin/assets/images/<?= htmlspecialchars($v['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>" style="height: 220px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title fw-bold mb-3"><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></h4>

                        <div class="mb-3">
                            <span class="badge bg-primary shadow-sm"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($v['nom_gamme']) ?></span>
                            <span class="badge bg-dark shadow-sm"><i class="fa-solid fa-car"></i> <?= htmlspecialchars($v['nom_carrosserie']) ?></span>
                        </div>

                        <ul class="list-group list-group-flush mb-4 flex-grow-1">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fa-solid fa-tag text-muted me-2"></i> Prix d'achat</span>
                                <strong class="fs-5"><?= number_format((float)$v['prix_achat'], 2, ',', ' ') ?> €</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fa-solid fa-key text-muted me-2"></i> Location</span>
                                <strong class="fs-5"><?= number_format((float)$v['prix_location'], 2, ',', ' ') ?> €/j</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fa-solid fa-shield-halved text-muted me-2"></i> Caution</span>
                                <span class="text-danger fw-bold"><?= number_format((float)$v['caution'], 2, ',', ' ') ?> €</span>
                            </li>
                        </ul>

                        <div class="mt-auto d-flex gap-2">
                            <a href="#" class="btn btn-outline-dark fw-bold flex-grow-1" <?= $v['status'] != 'Disponible' ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>
                                <i class="fa-solid fa-cart-shopping"></i> Acheter
                            </a>
                            <a href="#" class="btn btn-warning fw-bold text-dark flex-grow-1 shadow-sm" <?= $v['status'] != 'Disponible' ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>
                                <i class="fa-regular fa-clock"></i> Louer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>