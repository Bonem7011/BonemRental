<?php
// On instancie les DAO nécessaires pour l'affichage
$vehiculeDAO = new VehiculeDAO($cnx);
$gammeDAO = new GammeDAO($cnx);
$carrosserieDAO = new CarrosserieDAO($cnx);

$vehiculeToEdit = null;
$carrosseriesForEdit = [];

// MODE ÉDITION : Préparation des données si on modifie
if (isset($_GET['edit_id'])) {
    $vehiculeToEdit = $vehiculeDAO->getVehiculeById((int)$_GET['edit_id']);
    if ($vehiculeToEdit) {
        $carrosseriesForEdit = $carrosserieDAO->getCarrosseries();
    }
}

// Récupération des données pour les listes déroulantes et le tableau
$listeVehicules = $vehiculeDAO->getVehicules();
$listeGammes = $gammeDAO->getGammes();
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-car"></i> Gestion des Véhicules</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header <?= $vehiculeToEdit ? 'bg-warning text-dark' : 'bg-dark text-white' ?> fw-bold">
            <?= $vehiculeToEdit ? '<i class="fa-solid fa-pen-to-square"></i> Modifier le véhicule' : 'Ajouter un nouveau véhicule' ?>
        </div>
        <div class="card-body">
            <!-- Modification de l'action et ajout du enctype pour l'upload -->
            <form method="post" action="index_.php?page=traitement_vehicules.php" enctype="multipart/form-data" class="row g-3">

                <?php if ($vehiculeToEdit): ?>
                    <input type="hidden" name="id_vehicule" value="<?= $vehiculeToEdit['id_vehicule'] ?>">
                    <!-- On garde une trace de l'ancienne image pour ne pas la perdre si on n'en upload pas une nouvelle -->
                    <input type="hidden" name="image_actuelle" value="<?= htmlspecialchars($vehiculeToEdit['image']) ?>">
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Gamme</label>
                    <select name="id_gamme" id="id_gamme" class="form-select" required>
                        <option value="">Sélectionner une gamme...</option>
                        <?php foreach ($listeGammes as $g): ?>
                            <option value="<?= $g->id_gamme ?>" <?= ($vehiculeToEdit && $vehiculeToEdit['id_gamme'] == $g->id_gamme) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g->nom_gamme) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Carrosserie</label>
                    <select name="id_carrosserie" id="id_carrosserie" class="form-select" required>
                        <?php if ($vehiculeToEdit): ?>
                            <?php foreach ($carrosseriesForEdit as $c): ?>
                                <option value="<?= $c['id_carrosserie'] ?>" <?= $vehiculeToEdit['id_carrosserie'] == $c['id_carrosserie'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nom_carrosserie']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Sélectionner une gamme d'abord...</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Marque</label>
                    <input type="text" name="marque" class="form-control" value="<?= $vehiculeToEdit ? htmlspecialchars($vehiculeToEdit['marque']) : '' ?>" placeholder="ex: Volkswagen" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Modèle</label>
                    <input type="text" name="modele" class="form-control" value="<?= $vehiculeToEdit ? htmlspecialchars($vehiculeToEdit['modele']) : '' ?>" placeholder="ex: Golf 6" required>
                </div>

                <!-- Retrait des attributs 'required' sur les prix -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Prix Achat (€)</label>
                    <input type="number" step="0.01" name="prix_achat" class="form-control" value="<?= $vehiculeToEdit ? $vehiculeToEdit['prix_achat'] : '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Prix Loc. (€/jour)</label>
                    <input type="number" step="0.01" name="prix_location" class="form-control" value="<?= $vehiculeToEdit ? $vehiculeToEdit['prix_location'] : '' ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Caution (€)</label>
                    <input type="number" step="0.01" name="caution" class="form-control" value="<?= $vehiculeToEdit ? $vehiculeToEdit['caution'] : '0' ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="status" class="form-select">
                        <option value="Disponible" <?= ($vehiculeToEdit && $vehiculeToEdit['status'] == 'Disponible') ? 'selected' : '' ?>>Disponible</option>
                        <option value="Loué" <?= ($vehiculeToEdit && $vehiculeToEdit['status'] == 'Loué') ? 'selected' : '' ?>>Loué</option>
                        <option value="Vendu" <?= ($vehiculeToEdit && $vehiculeToEdit['status'] == 'Vendu') ? 'selected' : '' ?>>Vendu</option>
                    </select>
                </div>

                <!-- Modification pour gérer un vrai upload de fichier -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Photo du véhicule</label>
                    <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
                    <?php if ($vehiculeToEdit && !empty($vehiculeToEdit['image'])): ?>
                        <small class="text-muted">Fichier actuel : <?= htmlspecialchars($vehiculeToEdit['image']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="col-12 mt-3 text-end">
                    <?php if ($vehiculeToEdit): ?>
                        <a href="index_.php?page=vehicules.php" class="btn btn-secondary fw-bold me-2">Annuler</a>
                        <button type="submit" name="update_vehicule" class="btn btn-warning fw-bold text-dark"><i class="fa-solid fa-check"></i> Enregistrer les modifications</button>
                    <?php else: ?>
                        <button type="submit" name="add_vehicule" class="btn btn-success fw-bold"><i class="fa-solid fa-plus"></i> Enregistrer le véhicule</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
            <tr>
                <th>Classification</th>
                <th>Véhicule</th>
                <th>Prix Achat</th>
                <th>Prix Loc.</th>
                <th>Caution</th>
                <th>Statut</th>
                <th class="text-center col-actions-vehicule">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($listeVehicules)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Aucun véhicule en stock actuellement.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listeVehicules as $v): ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($v['nom_gamme']) ?></span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($v['nom_carrosserie']) ?></span>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></td>
                        <td><?= number_format((float)$v['prix_achat'], 2, ',', ' ') ?> €</td>
                        <td><?= number_format((float)$v['prix_location'], 2, ',', ' ') ?> €/jour</td>
                        <td class="text-danger fw-bold"><?= number_format((float)$v['caution'], 2, ',', ' ') ?> €</td>
                        <td>
                            <span class="badge <?= $v['status'] == 'Disponible' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= htmlspecialchars($v['status']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="index_.php?page=vehicules.php&edit_id=<?= $v['id_vehicule'] ?>" class="btn btn-sm btn-warning fw-bold text-dark me-1" title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="index_.php?page=traitement_vehicules.php&delete_id=<?= $v['id_vehicule'] ?>" class="btn btn-sm btn-danger fw-bold js-confirm-delete" data-message="Supprimer ce véhicule définitivement ?" title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



