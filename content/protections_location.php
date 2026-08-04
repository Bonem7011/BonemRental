<?php
// On s'assure que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Calcul du nombre de jours entre les deux dates
    $date_debut = new DateTime($_POST['date_debut']);
    $date_fin = new DateTime($_POST['date_fin']);
    $interval = $date_debut->diff($date_fin);
    $nb_jours = $interval->days;

    // Sécurité : au moins 1 jour de location
    if ($nb_jours < 1) {
        $nb_jours = 1;
    }

    // 2. Récupération des valeurs du formulaire
    $prix_journalier = floatval($_POST['prix_journalier']);
    $option_km_journalier = floatval($_POST['option_km']);

    // 3. Calculs des totaux de l'étape 1
    $frais_location = $prix_journalier * $nb_jours;
    $paquet_km = $option_km_journalier * $nb_jours;

    // Taxes
    $taxe_wltp = 2.36;
    $taxe_locale = 68.59;

    // 4. SAUVEGARDE DANS LA SESSION
    $_SESSION['reservation'] = [
        'id_vehicule' => $_POST['id_vehicule'],
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'nb_jours' => $nb_jours,
        'frais_location' => $frais_location,
        'paquet_km' => $paquet_km,
        'taxe_wltp' => $taxe_wltp,
        'taxe_locale' => $taxe_locale
    ];
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><a href="javascript:history.back()" class="text-dark text-decoration-none"><i class="bi bi-chevron-left"></i> DE QUELLE PROTECTION AVEZ-VOUS BESOIN ?</a></h2>
        <div class="text-end">
            <span class="fs-4 fw-bold">Total : 387,49 €</span><br>
            <a href="#" class="text-dark text-decoration-underline small" data-bs-toggle="modal" data-bs-target="#modalDetailsPrix">Détails du prix</a>
        </div>
    </div>

    <!-- Le formulaire qui envoie vers les options -->
    <form action="index_.php?page=options_location.php" method="POST"id="form-protections"
          data-prix-base="<?= $_SESSION['reservation']['frais_location'] ?? 218.63 ?>"
          data-nb-jours="<?= $_SESSION['reservation']['nb_jours'] ?? 3 ?>"
          data-paquet-km="<?= $_SESSION['reservation']['paquet_km'] ?? 10.94 ?>"
          data-taxe-wltp="<?= $_SESSION['reservation']['taxe_wltp'] ?? 2.36 ?>"
          data-taxe-locale="<?= $_SESSION['reservation']['taxe_locale'] ?? 68.59 ?>">

        <div class="row g-4">
            <!-- Carte 1 : Aucune protection -->
            <div class="col-md-3">
                <label class="card h-100 border-2 cursor-pointer p-3" for="protection_0">
                    <div class="form-check mb-3">
                        <input class="form-check-input fs-4" type="radio" name="protection" id="protection_0" value="0" checked>
                        <h4 class="form-check-label fw-bold ms-2">Aucune protection supplémentaire</h4>
                    </div>
                    <p class="text-danger small fw-bold">Franchise : jusqu'à la valeur du véhicule</p>
                    <ul class="list-unstyled small mt-3 mb-auto">
                        <li class="mb-2"><i class="bi bi-x text-muted"></i> Dommages : collision, rayures...</li>
                        <li class="mb-2"><i class="bi bi-x text-muted"></i> Pneus, pare-brise, vitres</li>
                    </ul>
                    <div class="mt-3 fs-5 fw-bold">Inclus</div>
                </label>
            </div>

            <!-- Carte 2 : Protection Basique -->
            <div class="col-md-3">
                <label class="card h-100 border-2 cursor-pointer p-3" for="protection_basic">
                    <div class="form-check mb-3">
                        <input class="form-check-input fs-4" type="radio" name="protection" id="protection_basic" value="8.48">
                        <h4 class="form-check-label fw-bold ms-2">Protection Basique</h4>
                    </div>
                    <p class="small fw-bold">Franchise : jusqu'à 1900,00 €</p>
                    <ul class="list-unstyled small mt-3 mb-auto">
                        <li class="mb-2"><i class="bi bi-check-lg text-success"></i> Dommages : collision, rayures...</li>
                        <li class="mb-2"><i class="bi bi-x text-muted"></i> Pneus, pare-brise, vitres</li>
                    </ul>
                    <div class="mt-3"><span class="fs-5 fw-bold">8,48 €</span> / jour</div>
                </label>
            </div>

            <!-- Carte 3 : Protection Intermédiaire -->
            <div class="col-md-3">
                <label class="card h-100 border-2 cursor-pointer p-3" for="protection_intermediaire">
                    <div class="form-check mb-3">
                        <input class="form-check-input fs-4" type="radio" name="protection" id="protection_intermediaire" value="22.38">
                        <h4 class="form-check-label fw-bold ms-2">Protection Intermédiaire</h4>
                    </div>

                    <div class="mb-2">
                        <i class="bi bi-star-fill text-dark"></i>
                        <i class="bi bi-star-fill text-dark"></i>
                        <i class="bi bi-star text-dark"></i>
                        <span class="badge border border-warning text-warning ms-2 rounded-pill">- 47% de remise en ligne</span>
                    </div>
                    <p class="small fw-bold text-success">Pas de franchise</p>

                    <ul class="list-unstyled small mt-3 mb-auto">
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Dommages : collision, rayures, chocs et vol</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Pneus, pare-brise, vitres</li>
                        <li class="mb-2"><i class="bi bi-x text-muted me-2"></i> Dommages intérieurs (ex : taches)</li>
                        <li class="mb-2"><i class="bi bi-x text-muted me-2"></i> Assistance dépannage : incidents courants, perte de clés...</li>
                    </ul>

                    <div class="mt-3">
                        <span class="fs-5 fw-bold">22,38 €</span> <span class="small">/ jour</span>
                        <span class="text-decoration-line-through text-muted small ms-2">42,22 € / jour</span>
                    </div>
                </label>
            </div>

            <!-- Carte 4 : Protection Complète -->
            <div class="col-md-3">
                <label class="card h-100 border-2 cursor-pointer p-3 border-dark" for="protection_complete">
                    <div class="form-check mb-3">
                        <input class="form-check-input fs-4 bg-dark border-dark" type="radio" name="protection" id="protection_complete" value="25.29">
                        <h4 class="form-check-label fw-bold ms-2">Protection Complète</h4>
                    </div>

                    <div class="mb-2">
                        <i class="bi bi-star-fill text-dark"></i>
                        <i class="bi bi-star-fill text-dark"></i>
                        <i class="bi bi-star-fill text-dark"></i>
                        <span class="badge border border-warning text-warning ms-2 rounded-pill">- 55% de remise en ligne</span>
                    </div>
                    <p class="small fw-bold text-success">Pas de franchise</p>

                    <ul class="list-unstyled small mt-3 mb-auto">
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Dommages : collision, rayures, chocs et vol</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Pneus, pare-brise, vitres</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Dommages intérieurs (ex : taches)</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Assistance dépannage : incidents courants, perte de clés...</li>
                    </ul>

                    <div class="mt-3">
                        <span class="fs-5 fw-bold">25,29 €</span> <span class="small">/ jour</span>
                        <span class="text-decoration-line-through text-muted small ms-2">56,19 € / jour</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-warning text-white fw-bold px-5 py-2">Continuer</button>
        </div>
    </form>
</div>
<!-- Modale Détails du prix -->
<div class="modal fade" id="modalDetailsPrix" tabindex="-1" aria-labelledby="modalDetailsPrixLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title fw-bold text-uppercase" id="modalDetailsPrixLabel">Détails du prix</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">

                <h6 class="fw-bold mb-3">Frais de location</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span id="modal-duree">3 Jours de location x 72,88 €</span>
                    <span id="modal-prix-base">218,63 €</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span>Paquet kilométrique</span>
                    <span>10,94 €</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-3 mb-3 small">
                    <span id="modal-nom-protection">Protection</span>
                    <span id="modal-prix-protection">0,00 €</span>
                </div>

                <!-- Section Taxes avec effet déroulant (Collapse Bootstrap) -->
                <div class="d-flex justify-content-between fw-bold mb-3 small"
                     data-bs-toggle="collapse"
                     data-bs-target="#collapseTaxes"
                     aria-expanded="false"
                     aria-controls="collapseTaxes"
                     style="cursor: pointer;">
                    <span>Taxes (TVA) et frais <i class="bi bi-chevron-down ms-1"></i></span>
                    <span id="modal-taxes-total">0,00 €</span>
                </div>

                <!-- Le contenu caché qui se déroule -->
                <div class="collapse mb-4" id="collapseTaxes">
                    <div class="d-flex justify-content-between mb-2 small text-muted ps-3">
                        <span>Taxe d'immatriculation (WLTP)</span>
                        <span id="modal-taxe-wltp">0,00 €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted ps-3">
                        <span>Supplément local</span>
                        <span id="modal-taxe-local">0,00 €</span>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Réduction</h6>
                <div class="d-flex justify-content-between border-bottom pb-3 mb-3 text-dark small">
                    <span>Remise sur la protection</span>
                    <span id="modal-remise">0,00 €</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="fs-5 fw-bold">Total (TTC)</span>
                    <span class="fs-3 fw-bold" id="modal-total-ttc">0,00 €</span>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="assets/js/protection.js"></script>