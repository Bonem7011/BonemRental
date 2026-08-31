<?php
class UploadManager {
    public static function gererUploadImage($fileInputName, $imageParDefaut = 'default.jpg') {
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
                $destination = __DIR__ . '/../../../assets/images/' . $newName;

                if (move_uploaded_file($tmpName, $destination)) {
                    return $newName; // Succès : on retourne le nouveau nom
                }
            }
        }
        // En cas d'erreur ou d'absence de fichier, on garde l'ancienne image ou 'default.jpg'
        return $imageParDefaut;
    }
}