<?php
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
// 1. Sauvegarde des options choisies et calcul du total final
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_options = 0;
    $nb_jours = $_SESSION['reservation']['nb_jours'] ?? 1;

    // Tableau pour mémoriser quelles options ont été choisies (utile pour la facture finale)
    $_SESSION['reservation']['options_choisies_details'] = [];

    // --- Récupération des Checkboxes (Switch) ---
    // Les options facturées à la journée
    $options_jour = ['conducteur_supp', 'pneus_hiver', 'chaines', 'moteur_diesel', 'options_confort'];
    foreach ($options_jour as $opt) {
        if (isset($_POST[$opt])) {
            $prix = floatval($_POST[$opt]);
            $cout = $prix * $nb_jours;
            $total_options += $cout;
            $_SESSION['reservation']['options_choisies_details'][] = $opt;
        }
    }

    // Les options facturées une seule fois (prix fixe)
    $options_fixe = ['service_plein', 'couverture_inter'];
    foreach ($options_fixe as $opt) {
        if (isset($_POST[$opt])) {
            $prix = floatval($_POST[$opt]);
            $total_options += $prix;
            $_SESSION['reservation']['options_choisies_details'][] = $opt;
        }
    }

    // --- Récupération des Selects (Listes déroulantes - facturés au jour) ---
    $options_select = ['siege_bebe', 'siege_enfant', 'rehausseur', 'porte_skis'];
    foreach ($options_select as $opt) {
        if (isset($_POST[$opt]) && floatval($_POST[$opt]) > 0) {
            $prix_unitaire_par_jour = floatval($_POST[$opt]);
            $cout = $prix_unitaire_par_jour * $nb_jours;
            $total_options += $cout;
            $_SESSION['reservation']['options_choisies_details'][] = $opt;
        }
    }

    $_SESSION['reservation']['total_options'] = $total_options;
}

// 2. Préparation des variables de prix pour l'affichage
$frais_location = $_SESSION['reservation']['frais_location'] ?? 0;
$paquet_km = $_SESSION['reservation']['paquet_km'] ?? 0;
$taxe_wltp = $_SESSION['reservation']['taxe_wltp'] ?? 0;
$taxe_locale = $_SESSION['reservation']['taxe_locale'] ?? 0;
$total_protection = $_SESSION['reservation']['total_protection'] ?? 0;
$remise = $_SESSION['reservation']['remise'] ?? 0;
$total_options = $_SESSION['reservation']['total_options'] ?? 0;

// Calcul du VRAI total final
$total_final = $frais_location + $paquet_km + $taxe_wltp + $taxe_locale + $total_protection + $remise + $total_options;
$_SESSION['reservation']['prix_total_ttc'] = $total_final;




// Assure-toi que ton fichier de connexion à la BDD et tes classes (DAO) sont bien inclus avant
// (Généralement géré par ton index_.php ou un autoloader)

// 1. Récupération des dates et de l'ID du véhicule depuis TA session
$id_vehicule = $_SESSION['reservation']['id_vehicule'] ?? null;
$date_depart = $_SESSION['reservation']['date_debut'] ?? "Date non définie";
$date_retour = $_SESSION['reservation']['date_fin'] ?? "Date non définie";

// (Si tu as aussi stocké le lieu dans la session, récupère-le ici, sinon on met une valeur par défaut)
$lieu_depart = "Aéroport de Bruxelles";
$lieu_retour = "Aéroport de Bruxelles";

// 2. Récupération des infos du véhicule via ton DAO
$vehiculeDAO = new VehiculeDAO($cnx);
$vehiculeInfo = null;
$voiture_nom = "Véhicule introuvable";

if ($id_vehicule) {
    // Utilisation de TA méthode
    $vehiculeInfo = $vehiculeDAO->getVehiculeById($id_vehicule);

    if ($vehiculeInfo) {
        // Concaténation de la marque et du modèle comme tu l'as fait dans ton catalogue
        $voiture_nom = htmlspecialchars($vehiculeInfo['marque']) . ' ' . htmlspecialchars($vehiculeInfo['modele']);
    }
}


?>


<div class="container my-5" style="font-family: Arial, sans-serif;">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <h2>
            <a href="javascript:history.back()" class="text-dark text-decoration-none fw-bold">
                <i class="bi bi-chevron-left"></i> REVOIR VOTRE RÉSERVATION
            </a>
        </h2>
        <div class="text-end">
            <span class="fs-4 fw-bold">Total : <?= number_format($total_final, 2, ',', ' ') ?> €</span><br>
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetailsPrixRecap" class="text-decoration-underline text-dark small">Détails du prix</a>
        </div>
    </div>

    <!-- Début du formulaire final -->
    <form action="index_.php?page=traitement_final.php" method="POST">
        <div class="row">

            <!-- COLONNE DE GAUCHE : Informations Conducteur -->
            <div class="col-lg-8 pe-lg-5">
                <h4 class="fw-bold mb-4">Qui va conduire ?</h4>

                <!-- Email -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Adresse e-mail</label>
                    <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="ex: alan.bombo@icloud.com" required>
                </div>

                <!-- Prénom & Nom -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label small fw-bold text-muted">Prénom</label>
                        <input type="text" name="prenom" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Nom de famille</label>
                        <input type="text" name="nom" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                </div>

                <!-- Pays & Téléphone -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label small fw-bold text-muted">Pays</label>
                        <select name="pays" class="form-select form-select-lg bg-light border-0">
                            <option value="BE" selected>🇧🇪 Belgique</option>
                            <option value="FR">🇫🇷 France</option>
                            <option value="LU">🇱🇺 Luxembourg</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted">Numéro de téléphone</label>
                        <input type="tel" name="telephone" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                </div>

                <!-- Informations WhatsApp (Design similaire à la vidéo) -->
                <div class="mb-5">
                    <div class="form-check mb-2">
                        <input class="form-check-input text-dark" type="radio" name="whatsapp" id="wa_oui" value="1" checked>
                        <label class="form-check-label" for="wa_oui">
                            <i class="bi bi-whatsapp text-success me-1"></i> Oui, je souhaite recevoir des messages automatisés via WhatsApp concernant ma réservation.
                        </label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="radio" name="whatsapp" id="wa_non" value="0">
                        <label class="form-check-label text-muted" for="wa_non">
                            Non, je ne souhaite pas recevoir de messages WhatsApp.
                        </label>
                    </div>

                    <div class="d-flex align-items-start text-muted small bg-light p-3 rounded">
                        <i class="bi bi-info-circle fs-5 me-2"></i>
                        <span>Les conducteurs doivent avoir leur permis de conduire depuis au moins 2 an(s) pour ce véhicule.</span>
                    </div>
                </div>

                <!-- Numéro de vol -->
                <h4 class="fw-bold mb-3 mt-5">Entrez le numéro de vol</h4>
                <p class="text-muted small mb-4">Ajoutez votre numéro de vol, nous le suivrons en temps réel, vous n'avez donc pas besoin de nous informer des modifications. En cas de retard, nous conserverons votre réservation jusqu'à 24 heures.</p>

                <div class="mb-5 w-50">
                    <label class="form-label small fw-bold text-muted">Numéro de vol <span class="fw-normal">(optionnel)</span></label>
                    <input type="text" name="numero_vol" class="form-control form-control-lg bg-light border-0">
                </div>

            </div>

            <!-- COLONNE DE DROITE : Récapitulatif (Sidebar fixe) -->
            <div class="col-lg-4">
                <div class="card border-0 bg-light p-4 sticky-top" style="top: 20px;">

                    <!-- Voiture -->
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-car-front-fill fs-1 me-3 text-dark"></i>
                        <div>
                            <h5 class="fw-bold mb-0"><?= htmlspecialchars($voiture_nom) ?></h5>
                            <span class="badge bg-secondary mt-1">Automatique</span>
                        </div>
                    </div>

                    <hr class="opacity-25 mb-4">

                    <!-- Prise en charge et retour -->
                    <!-- Voiture -->
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-car-front-fill fs-1 me-3 text-dark"></i>
                        <div>
                            <!-- Affichage dynamique du nom de TA voiture -->
                            <h5 class="fw-bold mb-0"><?= $voiture_nom ?></h5>
                            <span class="badge bg-secondary mt-1">Automatique</span>
                        </div>
                    </div>

                    <hr class="opacity-25 mb-4">

                    <!-- Prise en charge et retour -->
                    <h6 class="fw-bold mb-3">Prise en charge et retour</h6>

                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold small">Prise en charge</div>
                            <div class="fw-bold"><?= htmlspecialchars($lieu_depart) ?></div>
                            <!-- Affichage dynamique de TA date de départ -->
                            <div class="text-muted small"><?= htmlspecialchars($date_depart) ?></div>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <i class="bi bi-geo-alt-fill fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold small">Retour</div>
                            <div class="fw-bold"><?= htmlspecialchars($lieu_retour) ?></div>
                            <!-- Affichage dynamique de TA date de retour -->
                            <div class="text-muted small"><?= htmlspecialchars($date_retour) ?></div>
                        </div>
                    </div>

                    <hr class="opacity-25 mb-4">

                    <!-- Ce qui est inclus -->
                    <h6 class="fw-bold mb-3">Ce qui est inclus</h6>
                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check2 text-success fs-5 me-2"></i> Assurance au tiers</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check2 text-success fs-5 me-2"></i> Assistance dépannage 24/7</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check2 text-success fs-5 me-2"></i> Kilométrage : <?php echo (($_SESSION['reservation']['option_km'] ?? null) == '0') ? '900 km' : 'Illimité'; ?></li>
                        <?php if ($total_protection > 0): ?>
                            <li class="mb-2 d-flex align-items-center"><i class="bi bi-check2 text-success fs-5 me-2"></i> Protection Complète sélectionnée</li>
                        <?php endif; ?>
                        <?php if ($total_options > 0): ?>
                            <li class="mb-2 d-flex align-items-center"><i class="bi bi-check2 text-success fs-5 me-2"></i> Options supplémentaires incluses</li>
                        <?php endif; ?>
                    </ul>

                    <!-- Total final et bouton d'action -->
                    <div class="mt-4 pt-3 border-top border-dark">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0">Total</h4>
                            <h4 class="fw-bold mb-0"><?= number_format($total_final, 2, ',', ' ') ?> €</h4>
                        </div>
                        <p class="text-muted" style="font-size: 11px;">Toutes taxes comprises. Le montant sera prélevé selon nos conditions générales.</p>

                        <!-- Le bouton "Réserver" soumettra tout le formulaire (Infos client + Options calculées) -->
                        <button type="submit" class="btn w-100 fw-bold py-3 fs-5" style="background-color: #ff5f00; color: white; border-radius: 0;">
                            Réserver
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>
    <!-- Modale Détails du prix -->
    <div class="modal fade" id="modalDetailsPrixRecap" tabindex="-1" aria-labelledby="modalDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4" id="modalDetailsLabel">DÉTAILS DU PRIX</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Frais de location (<?= $nb_jours ?> jour(s))</span>
                        <span><?= number_format($frais_location, 2, ',', ' ') ?> €</span>
                    </div>

                    <?php if ($total_protection > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Protection Complète</span>
                            <span><?= number_format($total_protection, 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($total_options > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Options supplémentaires</span>
                            <span><?= number_format($total_options, 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endif; ?>

                    <hr class="my-3 opacity-25">

                    <div class="d-flex justify-content-between mb-2 fw-bold text-muted">
                        <span>Taxes (TVA) et frais</span>
                        <span><?= number_format($taxe_wltp + $taxe_locale, 2, ',', ' ') ?> €</span>
                    </div>

                    <hr class="border-dark my-3">

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total (TTC)</span>
                        <span><?= number_format($total_final, 2, ',', ' ') ?> €</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
