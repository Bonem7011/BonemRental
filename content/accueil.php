<?php
$vehiculeDAO = new VehiculeDAO($cnx);
$gammeDAO = new GammeDAO($cnx);
$carrosserieDAO = new CarrosserieDAO($cnx); // On a besoin des carrosseries pour le menu déroulant

// Récupération des critères tapés par le client dans l'URL (GET)
$recherche = $_GET['recherche'] ?? '';
$id_gamme = $_GET['id_gamme'] ?? '';
$id_carrosserie = $_GET['id_carrosserie'] ?? '';

// On appelle notre nouvelle méthode ultra-puissante
$listeVehicules = $vehiculeDAO->getVehiculesFiltres($recherche, $id_gamme, $id_carrosserie);

// On récupère les listes pour remplir les menus déroulants des filtres
$listeGammes = $gammeDAO->getGammes();
$listeCarrosseries = $carrosserieDAO->getCarrosseries();
?>

<div class="text-center py-5">
    <h1 class="display-4 fw-bold"><i class="fa-solid fa-car-side"></i> Bienvenue sur BonemRental</h1>
    <p class="lead mt-3">La plateforme de référence pour l'achat et la location de véhicules d'exception.</p>
</div>

<div class="card shadow-sm border-0 mb-5 bg-dark text-white">
    <div class="card-body p-4">
        <form method="get" action="index_.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="accueil.php">

            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Recherche</label>
                <input type="text" name="recherche" class="form-control" placeholder="Marque, modèle..." value="<?= htmlspecialchars($recherche) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Gamme</label>
                <select name="id_gamme" class="form-select">
                    <option value="">Toutes les gammes</option>
                    <?php foreach ($listeGammes as $g): ?>
                        <option value="<?= $g->id_gamme ?>" <?= ($id_gamme == $g->id_gamme) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g->nom_gamme) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Carrosserie</label>
                <select name="id_carrosserie" class="form-select">
                    <option value="">Toutes les carrosseries</option>
                    <?php foreach ($listeCarrosseries as $c): ?>
                        <option value="<?= $c['id_carrosserie'] ?>" <?= ($id_carrosserie == $c['id_carrosserie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom_carrosserie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-bold flex-grow-1 text-dark">Filtrer</button>
                <?php if (!empty($recherche) || !empty($id_gamme) || !empty($id_carrosserie)): ?>
                    <a href="index_.php?page=accueil.php" class="btn btn-outline-light" title="Réinitialiser"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">

    <?php if (empty($listeVehicules)): ?>
        <div class="col-12 text-center">
            <div class="alert alert-light border shadow-sm py-5">
                <i class="fa-solid fa-car-burst fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun véhicule ne correspond à votre recherche.</h4>
                <p class="mb-0">Essayez de modifier vos filtres ou de retirer certains critères.</p>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($listeVehicules as $v): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 m-3 z-3">
                        <span class="badge rounded-pill <?= $v['status'] == 'Disponible' ? 'bg-success' : 'bg-warning text-dark' ?> shadow border border-white">
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
                            <a href="index_.php?page=commande.php&id_vehicule=<?= $v['id_vehicule'] ?>&type=Achat" class="btn btn-outline-dark fw-bold flex-grow-1" <?= $v['status'] != 'Disponible' ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>
                                <i class="fa-solid fa-cart-shopping"></i> Acheter
                            </a>
                            <a href="index_.php?page=commande.php&id_vehicule=<?= $v['id_vehicule'] ?>&type=Location" class="btn btn-warning fw-bold text-dark flex-grow-1 shadow-sm" <?= $v['status'] != 'Disponible' ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>
                                <i class="fa-regular fa-clock"></i> Louer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>