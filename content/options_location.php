<?php
// 1. Sauvegarde du choix de protection dans la session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['protection'])) {
    $_SESSION['reservation']['prix_protection_jour'] = floatval($_POST['protection']);

    // Calcul du coût total de la protection sur la durée
    $nb_jours = $_SESSION['reservation']['nb_jours'] ?? 1;
    $_SESSION['reservation']['total_protection'] = $_SESSION['reservation']['prix_protection_jour'] * $nb_jours;

    // Ajout de la remise (à dynamiser selon tes règles métier)
    if ($_SESSION['reservation']['prix_protection_jour'] > 0) {
        $_SESSION['reservation']['remise'] = -93.73; // Remise fixe si une assurance payante est choisie
    } else {
        $_SESSION['reservation']['remise'] = 0;
    }
}

// 2. Préparation des variables pour l'affichage et le JS
$frais_location = $_SESSION['reservation']['frais_location'] ?? 0;
$paquet_km = $_SESSION['reservation']['paquet_km'] ?? 0;
$taxe_wltp = $_SESSION['reservation']['taxe_wltp'] ?? 0;
$taxe_locale = $_SESSION['reservation']['taxe_locale'] ?? 0;
$total_protection = $_SESSION['reservation']['total_protection'] ?? 0;
$remise = $_SESSION['reservation']['remise'] ?? 0;
$nb_jours = $_SESSION['reservation']['nb_jours'] ?? 1;

// 3. Calcul du montant de base AVANT les options
$sous_total_avant_options = $frais_location + $paquet_km + $taxe_wltp + $taxe_locale + $total_protection + $remise;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><a href="javascript:history.back()" class="text-dark text-decoration-none"><i class="bi bi-chevron-left"></i> DE QUELLES OPTIONS AVEZ-VOUS BESOIN ?</a></h2>
    <div class="text-end">
        <!-- Ajout de l'ID pour le JS -->
        <span class="fs-4 fw-bold" id="affichage_total_haut">Total : 387,49 €</span><br>
        <!-- Ajout des attributs pour ouvrir la modale -->
        <a href="#" class="text-decoration-underline text-dark small" data-bs-toggle="modal" data-bs-target="#detailsPrixModal">Détails du prix</a>
    </div>
</div>

<form action="index_.php?page=recap_location.php" method="POST"
      id="form-options"
      data-sous-total="<?= $sous_total_avant_options ?>"
      data-nb-jours="<?= $nb_jours ?>">

    <div class="row">
        <!-- Colonne de gauche : Liste des options -->
        <div class="col-lg-8">

            <!-- Option 1 : Conducteur supplémentaire -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-plus fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Conducteur supplémentaire</div>
                        <div class="small text-muted">11,90 € / jour et conducteur</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="conducteur_supp" value="11.90" data-type="jour">
                    </div>
                </div>
            </div>

            <!-- Option 2 : Siège bébé -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-hearts fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Siège bébé</div>
                        <div class="small text-muted">14,49 € / par unité (par jour)</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <select name="siege_bebe" class="form-select form-select-sm option-item" data-type="jour" style="width: 70px;">
                        <option value="0">0</option>
                        <option value="14.49">1</option>
                        <option value="28.98">2</option>
                    </select>
                </div>
            </div>

            <!-- Service de plein/recharge -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-ev-station fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Service de plein/recharge</div>
                        <div class="small text-muted">27,99 € / une fois</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="service_plein" value="27.99" data-type="fixe">
                    </div>
                </div>
            </div>

            <!-- Couverture internationale -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-globe fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Couverture internationale</div>
                        <div class="small text-muted">22,46 € / une fois</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="couverture_inter" value="22.46" data-type="fixe">
                    </div>
                </div>
            </div>

            <!-- Siège enfant -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-emoji-smile fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Siège enfant</div>
                        <div class="small text-muted">14,49 € / par unité (par jour)</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <select name="siege_enfant" class="form-select form-select-sm option-item" data-type="jour" style="width: 70px;">
                        <option value="0">0</option>
                        <option value="14.49">1</option>
                        <option value="28.98">2</option>
                    </select>
                </div>
            </div>

            <!-- Rehausseur garanti -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-up fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Rehausseur garanti</div>
                        <div class="small text-muted">13,49 € / par unité (par jour)</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <select name="rehausseur" class="form-select form-select-sm option-item" data-type="jour" style="width: 70px;">
                        <option value="0">0</option>
                        <option value="13.49">1</option>
                        <option value="26.98">2</option>
                    </select>
                </div>
            </div>

            <!-- Pneus adaptés pour l'hiver -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-snow fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Pneus adaptés pour l'hiver</div>
                        <div class="small text-muted">12,00 € / jour</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="pneus_hiver" value="12.00" data-type="jour">
                    </div>
                </div>
            </div>

            <!-- Chaînes antidérapantes -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-link fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Chaînes antidérapantes</div>
                        <div class="small text-muted">17,99 € / jour</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="chaines" value="17.99" data-type="jour">
                    </div>
                </div>
            </div>

            <!-- Porte-skis -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-truck-flatbed fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Porte-skis</div>
                        <div class="small text-muted">17,99 € / par unité (par jour)</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <select name="porte_skis" class="form-select form-select-sm option-item" data-type="jour" style="width: 70px;">
                        <option value="0">0</option>
                        <option value="17.99">1</option>
                        <option value="35.98">2</option>
                    </select>
                </div>
            </div>

            <!-- Moteur Diesel -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-fuel-pump fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Moteur Diesel</div>
                        <div class="small text-muted">7,98 € / jour</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="moteur_diesel" value="7.98" data-type="jour">
                    </div>
                </div>
            </div>

            <!-- Options de confort -->
            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-stars fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Options de confort</div>
                        <div class="small text-muted">5,99 € / jour</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="text-dark text-decoration-underline small me-4">Détails</a>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-4 option-item" type="checkbox" name="options_confort" value="5.99" data-type="jour">
                    </div>
                </div>
            </div>

        </div>



            <!-- Colonne de droite : Aperçu de la réservation (Sidebar fixe) -->
            <div class="col-lg-4">
                <div class="card bg-light border-0 p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4">Aperçu de votre réservation :</h5>

                    <ul class="list-unstyled small">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-lg text-success me-2 fs-5"></i>
                            <div>Assurance au tiers</div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-lg text-success me-2 fs-5"></i>
                            <div>Assistance dépannage 24/7</div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-lg text-success me-2 fs-5"></i>
                            <div>Kilométrage : <?php echo (($_SESSION['reservation']['option_km'] ?? null) == '0') ? '900 km' : 'Illimité'; ?></div>
                        </li>
                        <!-- On affichera la protection choisie à l'étape précédente -->
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-lg text-success me-2 fs-5"></i>
                            <div>Protection sélectionnée</div>
                        </li>
                    </ul>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-warning w-100 text-white fw-bold py-3 fs-5">Continuer</button>
                    </div>
                </div>
            </div>
    </div>
</form>

<!-- Modale Détails du prix -->
<div class="modal fade" id="detailsPrixModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-3">DÉTAILS DU PRIX</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">

                <!-- Résumé des étapes précédentes -->
                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span>Frais de base (Véhicule, Km, Assurances, Remises)</span>
                    <span><?= number_format($sous_total_avant_options, 2, ',', ' ') ?> €</span>
                </div>

                <!-- Ligne pour les options dynamiques -->
                <div class="d-flex justify-content-between mb-3 text-dark small fw-bold">
                    <span>Options supplémentaires</span>
                    <span id="modal-total-options">0,00 €</span>
                </div>

                <hr class="opacity-25">

                <!-- Total dynamique -->
                <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
                    <span>Total (TTC)</span>
                    <span id="modal-total-ttc">0,00 €</span>
                </div>

            </div>
        </div>
    </div>
</div>


<script src="assets/js/option.js"></script>