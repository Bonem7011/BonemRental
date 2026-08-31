<?php

// On instancie le DAO avec ta variable de connexion globale (généralement $cnx ou $pdo définie dans le routeur)
$commandeDAO = new CommandeDAO($cnx);
$locations = $commandeDAO->getToutesLesLocations();
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check text-dark me-2"></i> Gestion des Locations</h2>
    </div>


    <!-- GESTION DES MESSAGES D'ALERTE -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'restitution_ok'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Succès !</strong> Le véhicule a été restitué. Son statut est de nouveau "Disponible" et la commande est "Terminée".
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'erreur'): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Erreur :</strong> Impossible de restituer ce véhicule. Vérifiez qu'il s'agit bien d'une location en cours.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <!-- FIN GESTION DES MESSAGES -->



    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4">N° Cmd</th>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>Période</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($locations)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucune location en cours ou passée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($locations as $loc): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $loc['id_commande'] ?></td>
                                <td>
                                    <!-- htmlspecialchars empêche les failles XSS à l'affichage -->
                                    <div class="fw-bold"><?= htmlspecialchars($loc['prenom'] . ' ' . $loc['nom']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($loc['email']) ?></div>
                                </td>
                                <td>
                                    <?= htmlspecialchars($loc['marque'] . ' ' . $loc['modele']) ?>
                                </td>
                                <td>
                                    <div class="small"><strong>Départ:</strong> <?= date('d/m/Y', strtotime($loc['date_debut'])) ?></div>
                                    <div class="small"><strong>Retour:</strong> <?= date('d/m/Y', strtotime($loc['date_fin'])) ?></div>
                                </td>
                                <td class="fw-bold"><?= number_format($loc['montant_total'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <?php if ($loc['statut'] === 'Terminée'): ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($loc['statut']) ?></span>
                                    <?php else: ?>
                                        <!-- S'affichera pour "En cours" -->
                                        <span class="badge bg-warning"><?= htmlspecialchars($loc['statut']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Bouton pour restituer -->
                                    <a href="index_.php?page=action_restitution.php&id=<?= $loc['id_commande'] ?>" class="btn btn-success btn-sm btn-restituer">
                                        Restituer
                                    </a>

                                    <!-- Bouton pour voir les détails -->
                                    <a href="index_.php?page=details_location.php&id=<?= $loc['id_commande'] ?>" class="btn btn-outline-secondary btn-sm">
                                        Détails
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>