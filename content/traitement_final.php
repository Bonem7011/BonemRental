<?php
// 1. SÉCURITÉ : Vérifier si la session de réservation existe
if (!isset($_SESSION['reservation'])) {
    header('Location: index_.php?page=choix_transaction.php');
    exit;
}

// 2. SÉCURITÉ CLIENT
if (!isset($_SESSION['client']) || $_SESSION['client'] !== 1) {
    header('Location: index_.php?page=connexion.php');
    exit;
}

// 3. RÉCUPÉRATION DES DONNÉES UTILISATEUR
$id_client     = $_SESSION['id_client'];
$prenom_client = htmlspecialchars($_POST['prenom'] ?? $_SESSION['prenom_client'] ?? 'Client');
$nom_client    = htmlspecialchars($_POST['nom'] ?? '');
$email_client  = htmlspecialchars($_POST['email'] ?? '');

// 4. RÉCUPÉRATION DES DONNÉES DE LA RÉSERVATION (Correction de 'panier_location' vers 'reservation')
$id_vehicule = isset($_SESSION['reservation']['id_vehicule']) ? (int)$_SESSION['reservation']['id_vehicule'] : 0;

// Si l'ID est à 0, ça veut dire que le panier est perdu. On stoppe et on redirige vers le choix.
if ($id_vehicule === 0) {
    header('Location: index_.php?page=choix_transaction.php');
    exit;
}

// On récupère aussi les dates depuis 'reservation'
$date_depart = $_SESSION['reservation']['date_debut'] ?? 'Date inconnue';
$date_retour = $_SESSION['reservation']['date_fin'] ?? 'Date inconnue';
$total_final = $_SESSION['reservation']['prix_total_ttc'] ?? 0;

// 5. INSTANCIATION DES DAO
$vehiculeDAO = new VehiculeDAO($cnx);
$vehiculeInfo = $vehiculeDAO->getVehiculeById($id_vehicule);
$voiture_nom = $vehiculeInfo ? htmlspecialchars($vehiculeInfo['marque'] . ' ' . $vehiculeInfo['modele']) : "Véhicule";

// 6. INSERTION EN BASE DE DONNÉES
$commandeDAO = new CommandeDAO($cnx);
$type_commande = 'Location';

$commande_reussie = $commandeDAO->creerCommande(
        $id_client,
        $id_vehicule,
        $type_commande,
        $total_final,
        $date_depart,
        $date_retour
);

if ($commande_reussie) {
    $numero_reservation = rand(100000000, 999999999);
    // On vide la bonne session !
    unset($_SESSION['reservation']);
} else {
    die("Une erreur est survenue lors de l'enregistrement de votre réservation. Veuillez réessayer ou nous contacter.");
}
?>

<!-- En-tête sombre (Les classes bg-dark-header etc. doivent être dans ton CSS) -->
<div class="bg-dark-header text-white pt-5 pb-5 text-center">
    <div class="container">
        <div class="mb-3">
            <i class="bi bi-check-circle-fill text-success icon-huge"></i>
        </div>
        <p class="mb-1">Excellent choix, <?= $prenom_client . ' ' . $nom_client ?></p>
        <h1 class="fw-bold mb-3">VOTRE RÉSERVATION EST CONFIRMÉE</h1>
        <p class="text-light opacity-75">Nous avons envoyé un e-mail de confirmation à <?= $email_client ?></p>
    </div>
</div>

<!-- Carte superposée -->
<div class="container card-overlap mb-5">
    <div class="card overflow-hidden rounded-3 border-0 shadow-lg">

        <!-- Image de la voiture et titre -->
        <div class="bg-secondary bg-opacity-10 text-center position-relative pt-4">
            <img src="admin/assets/images/<?= htmlspecialchars($vehiculeInfo['image']); ?>" alt="<?= $voiture_nom; ?>" class="img-fluid img-car-recap">
            <div class="position-absolute bottom-0 start-0 p-4 text-start w-100 bg-gradient-dark-overlay">
                <h2 class="text-white fw-bold mb-0 text-uppercase"><?= $voiture_nom ?></h2>
                <span class="text-white opacity-75 small">ou similaire | Berline (UDAR)</span>
            </div>
        </div>

        <!-- Détails de la réservation -->
        <div class="card-body p-5">
            <div class="row">
                <!-- Colonne Itinéraire -->
                <div class="col-md-6 border-end pe-md-4">
                    <h4 class="fw-bold mb-1">Votre itinéraire</h4>
                    <p class="text-muted small mb-4">Numéro de réservation <?= $numero_reservation ?></p>

                    <div class="d-flex mb-4">
                        <i class="bi bi-airplane-fill fs-4 me-3 mt-1"></i>
                        <div>
                            <div class="text-muted small">Prise en charge</div>
                            <div class="fw-bold">Aéroport de Bruxelles</div>
                            <div class="text-muted small"><?= htmlspecialchars($date_depart) ?></div>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <i class="bi bi-airplane-fill fs-4 me-3 mt-1 rotate-90"></i>
                        <div>
                            <div class="text-muted small">Retour</div>
                            <div class="fw-bold">Aéroport de Bruxelles</div>
                            <div class="text-muted small"><?= htmlspecialchars($date_retour) ?></div>
                        </div>
                    </div>

                    <a href="index_.php?page=accueil.php" class="btn btn-warning text-white fw-bold px-4 rounded-pill mt-2">Retour à l'accueil</a>
                </div>

                <!-- Colonne Actions -->
                <div class="col-md-6 ps-md-5 mt-4 mt-md-0">
                    <h4 class="fw-bold mb-4">Est-ce que tout est correct ?</h4>

                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none mb-3">
                        <i class="bi bi-person-lines-fill fs-5 me-3"></i>
                        <span class="fw-bold border-bottom border-dark pb-1">Voir mes réservations</span>
                    </a>

                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none">
                        <i class="bi bi-printer fs-5 me-3"></i>
                        <span class="fw-bold border-bottom border-dark pb-1">Imprimer la confirmation</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
