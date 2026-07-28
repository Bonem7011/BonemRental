<?php
// L'instance $cnx est déjà fournie par all_includes.php via le routeur
$vehiculeDAO = new VehiculeDAO($cnx);

/**
 * Fonction utilitaire pour gérer l'upload de l'image de manière sécurisée
 */
function gererUploadImage($fileInputName, $imageParDefaut = 'default.jpg') {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileInputName]['tmp_name'];
        $fileName = basename($_FILES[$fileInputName]['name']);

        // Sécurisation de l'extension
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowedExtensions)) {
            // Création d'un nom de fichier unique pour éviter d'écraser des images existantes
            $newName = uniqid('vehicule_') . '.' . $ext;

            // Chemin de destination (à adapter si ton dossier images est ailleurs)
            // On remonte depuis admin/content/ vers assets/images/
            $destination = __DIR__ . '/../assets/images/' . $newName;

            if (move_uploaded_file($tmpName, $destination)) {
                return $newName; // Succès : on retourne le nouveau nom
            }
        }
    }
    // En cas d'erreur ou d'absence de fichier, on garde l'ancienne image ou 'default.jpg'
    return $imageParDefaut;
}

// ==========================================
// 1. TRAITEMENT DE L'AJOUT
// ==========================================
if (isset($_POST['add_vehicule'])) {
    // Récupération sécurisée et typage des variables
    $id_gamme = (int)($_POST['id_gamme'] ?? 0);
    $id_carrosserie = (int)($_POST['id_carrosserie'] ?? 0);
    $marque = htmlspecialchars(trim($_POST['marque'] ?? ''));
    $modele = htmlspecialchars(trim($_POST['modele'] ?? ''));

    // Si le champ est vide (ex: véhicule que pour la location), on passe NULL
    $prix_achat = !empty($_POST['prix_achat']) ? (float)$_POST['prix_achat'] : null;
    $prix_location = !empty($_POST['prix_location']) ? (float)$_POST['prix_location'] : null;
    $caution = (float)($_POST['caution'] ?? 0);

    $status = htmlspecialchars($_POST['status'] ?? 'Disponible');

    // Traitement de l'image
    $image_name = gererUploadImage('image', 'default.jpg');

    // Insertion via le DAO
    $vehiculeDAO->addVehicule($id_gamme, $id_carrosserie, $marque, $modele, $prix_achat, $prix_location, $caution, $status, $image_name);

    header("Location: index_.php?page=vehicules.php");
    exit();
}

// ==========================================
// 2. TRAITEMENT DE LA MODIFICATION
// ==========================================
if (isset($_POST['update_vehicule'])) {
    $id_vehicule = (int)($_POST['id_vehicule'] ?? 0);
    $id_gamme = (int)($_POST['id_gamme'] ?? 0);
    $id_carrosserie = (int)($_POST['id_carrosserie'] ?? 0);
    $marque = htmlspecialchars(trim($_POST['marque'] ?? ''));
    $modele = htmlspecialchars(trim($_POST['modele'] ?? ''));

    $prix_achat = !empty($_POST['prix_achat']) ? (float)$_POST['prix_achat'] : null;
    $prix_location = !empty($_POST['prix_location']) ? (float)$_POST['prix_location'] : null;
    $caution = (float)($_POST['caution'] ?? 0);

    $status = htmlspecialchars($_POST['status'] ?? 'Disponible');
    $image_actuelle = $_POST['image_actuelle'] ?? 'default.jpg';

    // Traitement de l'image (prend l'ancienne image s'il n'y a pas de nouvel upload)
    $image_name = gererUploadImage('image', $image_actuelle);

    // Mise à jour via le DAO
    $vehiculeDAO->updateVehicule($id_vehicule, $id_gamme, $id_carrosserie, $marque, $modele, $prix_achat, $prix_location, $caution, $status, $image_name);

    header("Location: index_.php?page=vehicules.php");
    exit();
}

// ==========================================
// 3. TRAITEMENT DE LA SUPPRESSION
// ==========================================
if (isset($_GET['delete_id'])) {
    $id_vehicule = (int)$_GET['delete_id'];
    $vehiculeDAO->deleteVehicule($id_vehicule);

    header("Location: index_.php?page=vehicules.php");
    exit();
}

// Redirection de sécurité si l'utilisateur accède à la page sans poster de formulaire
header("Location: index_.php?page=vehicules.php");
exit();
?>
